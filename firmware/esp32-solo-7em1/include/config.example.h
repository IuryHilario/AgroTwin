#pragma once

// Copie este arquivo para "config.h" (mesma pasta) e preencha com os seus
// dados antes de compilar. O config.h é ignorado pelo git (contém tokens).

// ---------------------------------------------------------------------------
// Wi-Fi
// ---------------------------------------------------------------------------
#define WIFI_SSID "NOME_DA_SUA_REDE"
#define WIFI_PASSWORD "SENHA_DA_SUA_REDE"

// ---------------------------------------------------------------------------
// AgroTwin — servidor
// ---------------------------------------------------------------------------
// URL base onde o Laravel está acessível pela rede local (sem barra no final).
// Ex.: "http://192.168.1.50:8000" (php artisan serve) ou a URL do Herd.
#define API_BASE_URL "http://192.168.1.50:8000"

// ---------------------------------------------------------------------------
// RS485 / Modbus
// ---------------------------------------------------------------------------
#define MODBUS_SLAVE_ID 1        // endereço do sensor (confira na etiqueta/manual)
#define MODBUS_BAUD_RATE 9600    // baud rate do sensor (confira na etiqueta/manual)
#define RS485_RX_PIN 16          // ESP32 RX2 <- RO do módulo MAX485
#define RS485_TX_PIN 17          // ESP32 TX2 -> DI do módulo MAX485
#define RS485_DE_RE_PIN 4        // ESP32 -> DE e RE do módulo MAX485 (ligados juntos)

// Intervalo entre leituras enviadas ao AgroTwin (ms).
#define READ_INTERVAL_MS 60000

// ---------------------------------------------------------------------------
// Sensores cadastrados no AgroTwin
// ---------------------------------------------------------------------------
// Para cada parâmetro, cadastre um Sensor no AgroTwin (mesma Lavoura) e copie
// o ID e o Token exibidos em "Detalhar" > "Integração com o Dispositivo".
// Deixe o TOKEN como "" para desativar o envio daquele parâmetro (ex.: se o
// seu sensor não tiver o registrador correspondente).

#define TOKEN_UMIDADE       ""
#define ID_UMIDADE          0

#define TOKEN_TEMPERATURA   ""
#define ID_TEMPERATURA      0

#define TOKEN_CONDUTIVIDADE ""
#define ID_CONDUTIVIDADE    0

#define TOKEN_PH            ""
#define ID_PH               0

#define TOKEN_NITROGENIO    ""
#define ID_NITROGENIO       0

#define TOKEN_FOSFORO       ""
#define ID_FOSFORO          0

#define TOKEN_POTASSIO      ""
#define ID_POTASSIO         0
