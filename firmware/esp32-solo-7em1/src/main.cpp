// Leitor ESP32 para o sensor de solo RS485 7-em-1 (umidade, temperatura,
// condutividade/EC, pH, nitrogênio, fósforo, potássio) via Modbus RTU,
// enviando cada leitura para a API de ingestão do AgroTwin.
//
// Antes de compilar: copie include/config.example.h para include/config.h e
// preencha Wi-Fi, URL do servidor e os tokens de cada sensor cadastrado no
// AgroTwin. Veja o README.md desta pasta para o esquema de ligação.

#include <Arduino.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <ModbusMaster.h>
#include "config.h"

// Endereços dos registradores Modbus (holding registers, function code 0x03).
// Este é o mapeamento mais comum para sensores RS485 7-em-1 deste tipo — se as
// leituras vierem estranhas, confira o manual/QR code que acompanha o sensor
// e ajuste os valores abaixo.
#define REG_UMIDADE       0x0000
#define REG_TEMPERATURA   0x0001
#define REG_CONDUTIVIDADE 0x0002
#define REG_PH            0x0003
#define REG_NITROGENIO    0x0004
#define REG_FOSFORO       0x0005
#define REG_POTASSIO      0x0006
#define REG_COUNT         7

ModbusMaster node;

// Intervalo entre leituras — começa com o valor padrão de config.h e é
// substituído pelo valor configurado no AgroTwin (por lavoura) assim que o
// dispositivo consegue buscar sua configuração pela primeira vez.
unsigned long intervaloLeituraMs = READ_INTERVAL_MS;

void modbusPreTransmission() {
    digitalWrite(RS485_DE_RE_PIN, HIGH);
}

void modbusPostTransmission() {
    digitalWrite(RS485_DE_RE_PIN, LOW);
}

void conectarWifi() {
    WiFi.mode(WIFI_STA);
    WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

    Serial.printf("Conectando ao Wi-Fi \"%s\"", WIFI_SSID);
    while (WiFi.status() != WL_CONNECTED) {
        delay(500);
        Serial.print(".");
    }
    Serial.printf("\nConectado! IP: %s\n", WiFi.localIP().toString().c_str());
}

// Envia uma leitura para a API do AgroTwin. Não faz nada se o sensor não
// estiver configurado (token vazio), permitindo desativar parâmetros que o
// seu sensor físico não possua.
void enviarLeitura(int sensorId, const char *token, float valor) {
    if (sensorId <= 0 || strlen(token) == 0) {
        return;
    }

    HTTPClient http;
    String url = String(API_BASE_URL) + "/api/sensores/" + String(sensorId) + "/leituras";

    http.begin(url);
    http.addHeader("Content-Type", "application/json");
    http.addHeader("Accept", "application/json");
    http.addHeader("Authorization", String("Bearer ") + token);

    JsonDocument doc;
    doc["valor"] = valor;
    String body;
    serializeJson(doc, body);

    int status = http.POST(body);
    Serial.printf("  -> sensor #%d valor=%.2f => HTTP %d\n", sensorId, valor, status);
    if (status != 200 && status != 201) {
        Serial.println("     " + http.getString());
    }

    http.end();
}

// Busca o intervalo de leitura configurado pelo agricultor para a lavoura
// deste sensor (tela de editar Lavoura no AgroTwin). Se a busca falhar por
// qualquer motivo, mantém o valor padrão de config.h.
void buscarIntervaloLeitura() {
    if (strlen(TOKEN_UMIDADE) == 0 || ID_UMIDADE <= 0) {
        return;
    }

    HTTPClient http;
    String url = String(API_BASE_URL) + "/api/sensores/" + String(ID_UMIDADE) + "/config";

    http.begin(url);
    http.addHeader("Accept", "application/json");
    http.addHeader("Authorization", String("Bearer ") + TOKEN_UMIDADE);

    int status = http.GET();
    if (status == 200) {
        JsonDocument doc;
        deserializeJson(doc, http.getString());
        intervaloLeituraMs = doc["intervalo_leitura_ms"] | READ_INTERVAL_MS;
        Serial.printf("Intervalo de leitura configurado no servidor: %lums\n", intervaloLeituraMs);
    } else {
        Serial.printf("Nao foi possivel buscar o intervalo configurado (HTTP %d), usando o padrao.\n", status);
    }

    http.end();
}

void setup() {
    Serial.begin(115200);
    delay(1000);

    pinMode(RS485_DE_RE_PIN, OUTPUT);
    digitalWrite(RS485_DE_RE_PIN, LOW);

    Serial2.begin(MODBUS_BAUD_RATE, SERIAL_8N1, RS485_RX_PIN, RS485_TX_PIN);
    node.begin(MODBUS_SLAVE_ID, Serial2);
    node.preTransmission(modbusPreTransmission);
    node.postTransmission(modbusPostTransmission);

    conectarWifi();
    buscarIntervaloLeitura();
}

void loop() {
    Serial.println("Lendo sensor via Modbus...");

    uint8_t resultado = node.readHoldingRegisters(REG_UMIDADE, REG_COUNT);

    if (resultado == node.ku8MBSuccess) {
        float umidade = node.getResponseBuffer(REG_UMIDADE) / 10.0;
        float temperatura = ((int16_t) node.getResponseBuffer(REG_TEMPERATURA)) / 10.0;
        float condutividade = node.getResponseBuffer(REG_CONDUTIVIDADE);
        float ph = node.getResponseBuffer(REG_PH) / 10.0;
        float nitrogenio = node.getResponseBuffer(REG_NITROGENIO);
        float fosforo = node.getResponseBuffer(REG_FOSFORO);
        float potassio = node.getResponseBuffer(REG_POTASSIO);

        Serial.printf(
            "Umidade=%.1f%% Temp=%.1fC EC=%.0f pH=%.1f N=%.0f P=%.0f K=%.0f\n",
            umidade, temperatura, condutividade, ph, nitrogenio, fosforo, potassio
        );

        if (WiFi.status() == WL_CONNECTED) {
            enviarLeitura(ID_UMIDADE, TOKEN_UMIDADE, umidade);
            enviarLeitura(ID_TEMPERATURA, TOKEN_TEMPERATURA, temperatura);
            enviarLeitura(ID_CONDUTIVIDADE, TOKEN_CONDUTIVIDADE, condutividade);
            enviarLeitura(ID_PH, TOKEN_PH, ph);
            enviarLeitura(ID_NITROGENIO, TOKEN_NITROGENIO, nitrogenio);
            enviarLeitura(ID_FOSFORO, TOKEN_FOSFORO, fosforo);
            enviarLeitura(ID_POTASSIO, TOKEN_POTASSIO, potassio);
        } else {
            Serial.println("Wi-Fi desconectado, tentando reconectar...");
            conectarWifi();
        }
    } else {
        Serial.printf("Falha na leitura Modbus (codigo 0x%02X)\n", resultado);
    }

    delay(intervaloLeituraMs);
}
