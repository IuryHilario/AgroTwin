#include <WiFi.h>
#include <HTTPClient.h>
#include <ModbusMaster.h>
#include <ArduinoJson.h>

// ===== CONFIGURAÇÃO WI-FI =====
const char* ssid = "NOME_WIFI"; // Nome do Wi-Fi
const char* password = "SENHA_WIFI"; // Senha do Wi-Fi

// ===== CONFIGURAÇÃO DO SERVIDOR =====
const char* servidor = "URL_COMPLETA_DO_SERVIDOR"; // Servidor (http://127.0.0.1)

// ===== CONFIGURAÇÃO DEEP SLEEP =====
#define TEMPO_SLEEP_PADRAO_SEGUNDOS 300 // 5 minutos entre ciclos normais
#define TEMPO_ESPERA_IRRIGACAO_SEGUNDOS 300 // 5 minutos entre checagens durante irrigacao
#define TEMPO_MAXIMO_IRRIGACAO_SEGUNDOS 3600 // seguranca: nunca irriga mais que 1 hora seguida
#define uS_TO_S_FACTOR 1000000ULL

// ===== CONFIGURAÇÃO MAX485 =====
#define MAX485_DE_RE 4
#define RX_PIN 16
#define TX_PIN 17

// ===== CONFIGURAÇÃO RELÉ / VÁLVULA =====
#define RELE_PIN 5
#define RELE_LIGADO LOW
#define RELE_DESLIGADO HIGH

ModbusMaster node;

struct SensorInfo {
  const char* nome;
  int id;
  const char* token;
};

SensorInfo sensores[] = {
  {"umidade",       1, "token_umidade"}, // Token Gerado ao Inserir o Sensor Umidade no Software
  {"ph",             2, "token_ph"}, // Token Gerado ao Inserir o Sensor PH no Software
  {"temperatura",    3, "token_temperatura"}, // Token Gerado ao Inserir o Sensor Temperatura no Software
  {"condutividade",  4, "token_condutividade"}, // Token Gerado ao Inserir o Sensor Condutividade no Software
  {"nitrogenio",     5, "token_nitrogenio"}, // Token Gerado ao Inserir o Sensor Nitrogenio no Software
  {"potassio",       6, "token_potassio"}, // Token Gerado ao Inserir o Sensor Potassio no Software
  {"fosforo",        7, "token_fosforo"} // Token Gerado ao Inserir o Sensor Fosforo no Software
};

const int NUM_SENSORES = 7; // Quantidade de buscas
const int INDICE_SENSOR_UMIDADE = 0; // Indice do sensor de umidade

void preTransmission() { digitalWrite(MAX485_DE_RE, HIGH); }
void postTransmission() { digitalWrite(MAX485_DE_RE, LOW); }

bool conectarWiFi(int timeoutSegundos = 15) {
  Serial.print("Conectando ao Wi-Fi");
  WiFi.begin(ssid, password);
  int tentativas = 0;
  while (WiFi.status() != WL_CONNECTED && tentativas < (timeoutSegundos * 2)) {
    delay(500);
    Serial.print(".");
    tentativas++;
  }
  if (WiFi.status() == WL_CONNECTED) {
    Serial.println(" conectado!");
    return true;
  }
  Serial.println(" falhou (timeout).");
  return false;
}

bool enviarLeitura(int sensorId, const char* token, float valor) {
  HTTPClient http;
  String url = String(servidor) + "/api/sensores/" + String(sensorId) + "/leituras";
  http.begin(url);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("Accept", "application/json");
  http.addHeader("Authorization", String("Bearer ") + token);

  JsonDocument doc;
  doc["valor"] = valor;
  String body;
  serializeJson(doc, body);

  int httpCode = http.POST(body);
  bool sucesso = (httpCode == 200 || httpCode == 201);
  Serial.print(sucesso ? "OK -> " : "FALHA (");
  if (!sucesso) { Serial.print(httpCode); Serial.print(") -> "); }
  Serial.println(url);
  http.end();
  return sucesso;
}

long buscarIntervaloSegundos() {
  HTTPClient http;
  String url = String(servidor) + "/api/sensores/" + String(sensores[0].id) + "/config";
  http.begin(url);
  http.addHeader("Accept", "application/json");
  http.addHeader("Authorization", String("Bearer ") + sensores[0].token);

  int httpCode = http.GET();
  long intervaloSegundos = TEMPO_SLEEP_PADRAO_SEGUNDOS;

  if (httpCode == 200) {
    String payload = http.getString();
    JsonDocument doc;
    if (!deserializeJson(doc, payload) && doc["intervalo_leitura_ms"].is<long>()) {
      intervaloSegundos = (long)doc["intervalo_leitura_ms"] / 1000;
    }
  }
  http.end();
  return intervaloSegundos;
}

bool buscarLimitesUmidade(float &valorMin, float &valorMax) {
  HTTPClient http;
  int sensorId = sensores[INDICE_SENSOR_UMIDADE].id;
  const char* token = sensores[INDICE_SENSOR_UMIDADE].token;

  String url = String(servidor) + "/api/sensores/" + String(sensorId) + "/limites";
  http.begin(url);
  http.addHeader("Accept", "application/json");
  http.addHeader("Authorization", String("Bearer ") + token);

  int httpCode = http.GET();
  bool sucesso = false;

  if (httpCode == 200) {
    String payload = http.getString();
    JsonDocument doc;
    if (!deserializeJson(doc, payload)) {
      if (!doc["valor_min"].isNull() && !doc["valor_max"].isNull()) {
        valorMin = doc["valor_min"];
        valorMax = doc["valor_max"];
        sucesso = true;
      }
    }
  }
  http.end();
  return sucesso;
}

// Le a umidade atual do sensor (retorna -1 em caso de erro)
float lerUmidade() {
  uint8_t result = node.readHoldingRegisters(0x0000, 7);
  if (result == node.ku8MBSuccess) {
    return node.getResponseBuffer(0) / 10.0;
  }
  return -1;
}

void irParaDeepSleep(long segundos) {
  Serial.print("Entrando em Deep Sleep por ");
  Serial.print(segundos);
  Serial.println("s...");
  digitalWrite(RELE_PIN, RELE_DESLIGADO); // garante valvula desligada antes de dormir
  WiFi.disconnect(true);
  esp_sleep_enable_timer_wakeup(segundos * uS_TO_S_FACTOR);
  Serial.flush();
  esp_deep_sleep_start();
}

void setup() {
  Serial.begin(115200);
  delay(500);
  Serial.println("=== AgroTwin - Ciclo com Histerese ===");

  pinMode(RELE_PIN, OUTPUT);
  digitalWrite(RELE_PIN, RELE_DESLIGADO);

  Serial2.begin(4800, SERIAL_8N1, RX_PIN, TX_PIN);
  pinMode(MAX485_DE_RE, OUTPUT);
  digitalWrite(MAX485_DE_RE, LOW);

  node.begin(1, Serial2);
  node.preTransmission(preTransmission);
  node.postTransmission(postTransmission);

  if (!conectarWiFi()) {
    irParaDeepSleep(TEMPO_SLEEP_PADRAO_SEGUNDOS);
    return;
  }

  // ===== LEITURA INICIAL DE TODOS OS PARAMETROS =====
  uint8_t result = node.readHoldingRegisters(0x0000, 7);

  if (result == node.ku8MBSuccess) {
    float valores[7];
    valores[0] = node.getResponseBuffer(0) / 10.0;
    valores[1] = node.getResponseBuffer(3) / 10.0;
    valores[2] = node.getResponseBuffer(1) / 10.0;
    valores[3] = node.getResponseBuffer(2);
    valores[4] = node.getResponseBuffer(4);
    valores[5] = node.getResponseBuffer(6);
    valores[6] = node.getResponseBuffer(5);

    Serial.println("-------- ENVIANDO LEITURAS --------");
    for (int i = 0; i < NUM_SENSORES; i++) {
      Serial.print(sensores[i].nome);
      Serial.print(": ");
      Serial.print(valores[i]);
      Serial.print(" -> ");
      enviarLeitura(sensores[i].id, sensores[i].token, valores[i]);
      delay(300);
    }
    Serial.println("------------------------------------");

    // ===== LOGICA DE HISTERESE =====
    float umidade = valores[INDICE_SENSOR_UMIDADE];
    float limiteMin, limiteMax;

    if (buscarLimitesUmidade(limiteMin, limiteMax)) {
      Serial.print("Umidade atual: ");
      Serial.print(umidade);
      Serial.print("%  |  Min: ");
      Serial.print(limiteMin);
      Serial.print("%  Max: ");
      Serial.println(limiteMax);

      if (umidade < limiteMin) {
        Serial.println(">>> Umidade abaixo do minimo. Iniciando irrigacao...");

        unsigned long inicioIrrigacaoTotal = millis();
        bool atingiuMaximo = false;

        while (!atingiuMaximo) {
          // Liga a valvula
          digitalWrite(RELE_PIN, RELE_LIGADO);
          Serial.println(">>> Valvula LIGADA.");

          // Espera o tempo definido (SEM deep sleep, para nao perder o estado do rele)
          Serial.print("Aguardando ");
          Serial.print(TEMPO_ESPERA_IRRIGACAO_SEGUNDOS);
          Serial.println("s antes de checar novamente...");
          delay((unsigned long)TEMPO_ESPERA_IRRIGACAO_SEGUNDOS * 1000);

          // Desliga temporariamente para medir (evita interferencia na leitura)
          digitalWrite(RELE_PIN, RELE_DESLIGADO);
          delay(1000);

          umidade = lerUmidade();
          Serial.print("Verificando... Umidade agora: ");
          Serial.print(umidade);
          Serial.println("%");

          if (umidade < 0) {
            Serial.println(">>> Erro na leitura durante irrigacao. Abortando por seguranca.");
            break;
          }

          if (umidade >= limiteMax) {
            atingiuMaximo = true;
            Serial.println(">>> Umidade atingiu o MAXIMO! Finalizando irrigacao.");
          }

          // Verificacao de seguranca: nunca irriga por mais que o tempo maximo definido
          if ((millis() - inicioIrrigacaoTotal) / 1000 > TEMPO_MAXIMO_IRRIGACAO_SEGUNDOS) {
            Serial.println(">>> TIMEOUT DE SEGURANCA atingido! Interrompendo irrigacao.");
            break;
          }
        }

        digitalWrite(RELE_PIN, RELE_DESLIGADO);
        Serial.println(">>> Irrigacao encerrada. Valvula desligada.");

        // Envia a leitura final apos a irrigacao
        enviarLeitura(sensores[INDICE_SENSOR_UMIDADE].id, sensores[INDICE_SENSOR_UMIDADE].token, umidade);

      } else {
        Serial.println(">>> Umidade dentro da faixa ideal. Nenhuma acao necessaria.");
      }
    } else {
      Serial.println("Nao foi possivel buscar limites. Irrigacao nao acionada por seguranca.");
    }

  } else {
    Serial.print("Erro na leitura do sensor. Codigo: 0x");
    Serial.println(result, HEX);
  }

  // ===== DEEP SLEEP NORMAL (so acontece quando NAO esta irrigando) =====
  long intervalo = buscarIntervaloSegundos();
  irParaDeepSleep(intervalo);
}

void loop() {}