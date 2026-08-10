// Controlador ESP32 da válvula solenoide de irrigação. Consulta
// periodicamente o AgroTwin para saber se a lavoura precisa ser irrigada
// agora (decisão tomada pelo servidor, com base na umidade do solo e nos
// limites configurados pelo agricultor — ver App\Services\IrrigacaoService)
// e liga/desliga o relé de acordo.
//
// Antes de compilar: copie include/config.example.h para include/config.h e
// preencha Wi-Fi, URL do servidor, ID da lavoura e o token de irrigação
// (copiado da tela "Monitorar" da lavoura no AgroTwin).

#include <Arduino.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include "config.h"

bool valvulaAberta = false;

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

void definirValvula(bool aberta) {
    if (aberta == valvulaAberta) {
        return;
    }

    valvulaAberta = aberta;
    bool nivelPino = RELE_ATIVO_EM_LOW ? !aberta : aberta;
    digitalWrite(RELE_PIN, nivelPino ? HIGH : LOW);

    Serial.println(aberta ? ">> Válvula ABERTA (irrigando)" : ">> Válvula FECHADA");
}

void consultarStatusIrrigacao() {
    HTTPClient http;
    String url = String(API_BASE_URL) + "/api/lavouras/" + String(ID_LAVOURA) + "/irrigacao";

    http.begin(url);
    http.addHeader("Accept", "application/json");
    http.addHeader("Authorization", String("Bearer ") + TOKEN_IRRIGACAO);

    int status = http.GET();

    if (status == 200) {
        JsonDocument doc;
        deserializeJson(doc, http.getString());
        definirValvula(doc["irrigar"] | false);
    } else {
        Serial.printf("Falha ao consultar status da irrigacao (HTTP %d)\n", status);
    }

    http.end();
}

void setup() {
    Serial.begin(115200);
    delay(1000);

    pinMode(RELE_PIN, OUTPUT);
    definirValvula(false);

    conectarWifi();
}

void loop() {
    if (WiFi.status() == WL_CONNECTED) {
        consultarStatusIrrigacao();
    } else {
        Serial.println("Wi-Fi desconectado, tentando reconectar...");
        conectarWifi();
    }

    delay(POLL_INTERVAL_MS);
}
