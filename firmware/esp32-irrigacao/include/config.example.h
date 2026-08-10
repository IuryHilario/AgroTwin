#pragma once

// Copie este arquivo para "config.h" (mesma pasta) e preencha com os seus
// dados antes de compilar. O config.h é ignorado pelo git (contém token).

// ---------------------------------------------------------------------------
// Wi-Fi
// ---------------------------------------------------------------------------
#define WIFI_SSID "NOME_DA_SUA_REDE"
#define WIFI_PASSWORD "SENHA_DA_SUA_REDE"

// ---------------------------------------------------------------------------
// AgroTwin — servidor
// ---------------------------------------------------------------------------
// URL base onde o Laravel está acessível pela rede local (sem barra no final).
#define API_BASE_URL "http://192.168.1.50:8000"

// ID da lavoura e token de irrigação — copie da tela "Monitorar" da lavoura
// no AgroTwin (seção "Irrigação").
#define ID_LAVOURA 0
#define TOKEN_IRRIGACAO ""

// ---------------------------------------------------------------------------
// Relé / válvula solenoide
// ---------------------------------------------------------------------------
#define RELE_PIN 26

// A maioria dos módulos relé de 1 canal é acionada em nível baixo (LOW liga,
// HIGH desliga). Se a sua válvula fizer o oposto do esperado, troque para false.
#define RELE_ATIVO_EM_LOW true

// Intervalo entre consultas ao servidor (ms). Não precisa ser tão frequente
// quanto a leitura dos sensores — a umidade do solo muda devagar.
#define POLL_INTERVAL_MS 30000
