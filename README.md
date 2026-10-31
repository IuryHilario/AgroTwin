# AgroTwin - Plataforma Inteligente de Gestão Agrícola

## 📋 Descrição

**AgroTwin** é uma plataforma para gestão integrada de propriedades agrícolas, desenvolvida com Laravel 12 e Blade. O sistema reúne propriedades, lavouras, insumos e sensores IoT de solo, e usa regras condicionais para gerar alertas, recomendações e controlar a irrigação. O foco são pequenos e médios produtores de hortaliças.

## 🎯 Objetivo

Oferecer ao agricultor uma solução acessível e de baixo custo para:
- Monitorar umidade, NPK, pH, temperatura e condutividade do solo
- Automatizar a irrigação com base nas faixas ideais de cada lavoura
- Gerenciar propriedades, lavouras e insumos
- Receber alertas quando o solo sai da faixa adequada
- Acompanhar o histórico de leituras e irrigações e o diagnóstico do solo

## 👥 Público-Alvo

- Pequenos e médios produtores de hortaliças
- Gestores de propriedades agrícolas
- Agrônomos e consultores agrícolas

## ✨ Funcionalidades

### 1. Monitoramento IoT do Solo
- Coleta de umidade, NPK (N, P, K), pH, temperatura e condutividade elétrica pelo sensor NPKPHCTH-S 7-em-1
- Envio das leituras pela API REST (`POST /api/sensores/{id}/leituras`), autenticada por token de dispositivo e com rate limiting
- Leituras armazenadas no MariaDB e avaliadas na chegada contra a faixa configurada da lavoura
- Intervalo de leitura configurável por lavoura, consultado pelo ESP32 (`GET /api/sensores/{id}/config`)
- Detecção de sensor sem comunicação pela data da última leitura

### 2. Irrigação Automática
- O ESP32 busca no servidor a faixa de umidade da lavoura (`GET /api/sensores/{id}/limites`)
- Abaixo do mínimo, liga a válvula solenoide e irriga até a umidade chegar ao máximo (histerese), com limite de segurança de 1 hora seguida
- O servidor registra o histórico de irrigações (início, fim, duração, tipo e motivo) e mostra o status no monitoramento da lavoura
- Ciclo com **Deep Sleep**: o ESP32 acorda, lê o sensor, envia as leituras, decide a irrigação e volta a dormir

### 3. Propriedades e Lavouras
- Cadastro de propriedade em etapas (Propriedade → Localidade → Finalização), com busca de município, GPS e mapa para marcar o ponto exato
- Lavouras com cultura, datas de plantio e colheita, status e intervalo de leitura
- Faixas ideais (mín./máx.) por parâmetro e por lavoura, com valores sugeridos por cultura preenchidos em um clique

### 4. Insumos
- Cadastro de fertilizantes, defensivos, sementes, adubos e corretivos
- Controle de estoque com entradas e saídas e registro de aplicação na lavoura
- Relatório de insumos com exportação em PDF e envio por e-mail

### 5. Alertas
- Alerta quando um parâmetro sai da faixa configurada (umidade, pH, temperatura, NPK, condutividade)
- Cada alerta é um **episódio**: abre quando o parâmetro sai da faixa, acumula as ocorrências enquanto continua fora e é encerrado quando volta (com histerese de 5%, para não reabrir a cada oscilação)
- Severidade calculada pelo desvio: *warning* ou *critical* (a partir de 30% além do limite)
- Marcar como lido e notificação por e-mail na abertura do episódio (fila assíncrona), ativável nas Configurações

### 6. Recomendações por Regras Condicionais
- Regras sobre os parâmetros do solo geram recomendações de manejo, salvas no histórico da lavoura
- A mesma recomendação não se repete dentro de 12 horas

### 7. Relatórios e Diagnóstico do Solo
- Índice de saúde do solo, situação de cada parâmetro em relação à faixa ideal e tendência no período
- Ações sugeridas ao produtor a partir das leituras (`DiagnosticoSoloService`)
- Gráficos históricos e exportação em PDF

### 8. Dashboard
- Seletor Propriedade → Lavoura, com os indicadores da lavoura selecionada
- Medidores que posicionam cada leitura dentro da faixa ideal, gráficos históricos, status dos sensores, alertas ativos e recomendações
- Perfil (dados da conta e troca de senha) e Configurações (notificação por e-mail e propriedade padrão)
- Tema claro/escuro e layout responsivo

### 9. Dados Climáticos (Open-Meteo)
- Tempo atual, chance de chuva nas próximas horas e previsão de 5 dias com chuva prevista e evapotranspiração (ET0), pelas coordenadas da propriedade
- Exibidos no dashboard, nos detalhes da propriedade e no cadastro
- Cache de 30 minutos e timeout curto: se a API falhar, a tela segue funcionando sem o bloco de clima

### 10. Segurança
- Login com limite de 5 tentativas por minuto e recuperação de senha por e-mail
- Isolamento multi-tenant: cada usuário só acessa os próprios dados
- Validação de todas as entradas por Form Requests e proteção CSRF

## 🛠️ Stack Tecnológico

### Hardware (IoT)
- **ESP32 DevKit V1** — microcontrolador com Wi-Fi nativo
- **Sensor NPKPHCTH-S 7-em-1** — umidade, NPK, pH, condutividade e temperatura (RS485/Modbus)
- **Conversor MAX485** — RS485 para TTL (sensor ↔ ESP32)
- **Módulo relé 1 canal** — interface ESP32 ↔ válvula
- **Válvula solenoide 12V** — acionamento da irrigação

### Comunicação
- **RS485 / Modbus RTU** — sensor ↔ ESP32
- **Wi-Fi 802.11 b/g/n** — conectividade do ESP32
- **API REST (HTTP/JSON)** — ESP32 ↔ Laravel, autenticada por token de dispositivo

### Backend
- **Laravel 12** (PHP 8.2+), arquitetura MVC com camadas Entity / Model / Service / Controller
- **MariaDB** — todos os dados: usuários, propriedades, lavouras, insumos, sensores, leituras, alertas, recomendações e histórico de irrigação
- **Filas (database)** — envio de e-mails em segundo plano
- **DomPDF** — exportação de relatórios em PDF
- Autenticação própria por e-mail e senha

### Frontend
- **Blade** com componentes anônimos reutilizáveis
- **Tailwind CSS 4** — estilo responsivo, tema claro/escuro
- **Alpine.js 3** — interatividade (sidebar, modais, formulário em etapas)
- **Chart.js** — gráficos históricos
- **Leaflet** — mapa do seletor de localidade
- **Vite 6** — build dos assets

### Integrações
- **Open-Meteo** — previsão do tempo, chance de chuva, ET0 e geocodificação de municípios, sem chave de API

### Qualidade
- **PHPUnit 11** — 105 testes automatizados
- **Laravel Pint** — padronização de código

## 🏗️ Arquitetura IoT em Camadas

```
┌─────────────────────────────────────────────────┐
│  CAMADA 4 — APLICAÇÃO                           │
│  Dashboard · Relatórios e diagnóstico do solo   │
│  Alertas · Recomendações · Gestão de insumos    │
├──────────────── HTTP (navegador) ───────────────┤
│  CAMADA 3 — PROCESSAMENTO / SERVIDOR            │
│  Backend Laravel · API REST · MariaDB           │
│  Regras condicionais · Fila de e-mails          │
│  Integração Open-Meteo                          │
├──────────────── HTTP/JSON (API REST) ───────────┤
│  CAMADA 2 — REDE / COMUNICAÇÃO                  │
│  ESP32 (gateway) · Wi-Fi · Pré-processamento    │
│  Controle da irrigação · Deep Sleep             │
├──────────────── RS485 / Modbus ─────────────────┤
│  CAMADA 1 — PERCEPÇÃO                           │
│  Sensor 7-em-1 · Relé · Válvula solenoide       │
└─────────────────────────────────────────────────┘
```

## 🔄 Fluxo do Sistema

```
ESP32 acorda do Deep Sleep
         │
         │ RS485/Modbus
         ▼
Lê o sensor 7-em-1 (umidade, NPK, pH, temperatura, condutividade)
         │
         │ HTTP/JSON (token do dispositivo)
         ▼
Backend Laravel recebe e grava no MariaDB
         │
         ├── Parâmetro fora da faixa? → Abre/atualiza o episódio de alerta
         │                              Gera recomendação · E-mail opcional (fila)
         │
         └── Registra o histórico de irrigação
         │
         ▼
ESP32 consulta a faixa de umidade da lavoura
         │
         ├── Umidade abaixo do mínimo? → Liga a válvula até atingir o máximo
         │                               (limite de segurança de 1 hora)
         ▼
ESP32 volta ao Deep Sleep até o próximo ciclo

Dashboard e relatórios exibem tudo por Propriedade → Lavoura,
junto com a previsão do tempo do Open-Meteo
```

## 📁 Estrutura de Pastas

```
AgroTwin/
├── app/
│   ├── Console/Commands/     # Comandos Artisan (ex.: simulação de leituras em ambiente local)
│   ├── Entity/               # Constantes de tabela/fillable/casts + queries estáticas
│   ├── Enums/                # TipoSensor, TipoStatus, TipoInsumo, etc.
│   ├── Http/
│   │   ├── Controllers/      # Controllers (shell) + subpastas por módulo (Tela/Crud) + Api/
│   │   └── Requests/         # Form Requests (validação)
│   ├── Mail/                 # E-mails (alerta, relatório de insumos)
│   ├── Models/               # Modelos Eloquent + traits Core/Dto/Insert/Update
│   ├── Services/             # Regras de negócio (leituras, alertas, irrigação, recomendações,
│   │                         #   diagnóstico do solo, clima, localidade, relatório de insumos)
│   ├── Support/              # Faixas ideais sugeridas por cultura
│   └── Traits/               # Traits reutilizáveis (escopo por usuário, entity)
├── database/
│   └── migrations/custom/    # Migrations do projeto
├── docs/                     # Documentação de componentes (stepper)
├── firmware/
│   └── esp32-solo-7em1/      # Firmware do ESP32 (PlatformIO)
├── resources/
│   ├── views/                # Templates Blade, um diretório por módulo + components/
│   ├── css/                  # Tailwind
│   └── js/                   # Alpine (componentes reutilizáveis) e módulos por tela
├── routes/
│   ├── web.php               # Rotas web
│   ├── api.php               # API REST do dispositivo
│   └── rotasWeb/             # Rotas separadas por módulo
└── tests/                    # Testes automatizados
```

## 🚀 Como Executar

### Pré-requisitos
- PHP 8.2+ e Composer
- Node.js 18+ e NPM
- MariaDB (ou MySQL)
- PlatformIO (ou a extensão do VS Code) para o firmware

### Instalação

1. **Clone o repositório**
   ```bash
   git clone https://github.com/IuryHilario/AgroTwin.git
   cd AgroTwin
   ```

2. **Instale as dependências**
   ```bash
   composer install
   npm install
   ```

3. **Configure o ambiente**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure o banco de dados no .env**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=agrotwin
   DB_USERNAME=user_agrotwin
   DB_PASSWORD=senha_segura

   # Notificações por e-mail
   MAIL_MAILER=log
   QUEUE_CONNECTION=database
   ```

   A previsão do tempo usa o Open-Meteo, que não exige chave de API.

5. **Execute as migrations e inicie**
   ```bash
   php artisan migrate
   composer run dev    # servidor + fila + logs + vite juntos
   ```

   Em ambiente local, o agendador simula leituras a cada 5 minutos (`sensores:simular-leitura`) para o dashboard ter dados sem o hardware.

6. **Firmware ESP32**
   ```
   1. Abra firmware/esp32-solo-7em1/ no VS Code com PlatformIO
   2. Copie include/config.example.h para include/config.h e preencha Wi-Fi, URL do servidor e tokens dos sensores
   3. "PlatformIO: Upload" com o ESP32 conectado por USB
   ```
   O ID e o token de cada sensor ficam em Sensores → Detalhar → "Integração com o Dispositivo". Esquema de ligação e mapeamento de registradores no README do firmware.

Acesse: `http://localhost:8000`

## 🧪 Testes

```bash
php artisan test
```

Os testes usam SQLite em memória (o PHP precisa da extensão `pdo_sqlite`) e nunca acessam a internet: chamadas ao Open-Meteo são simuladas com `Http::fake()`.

Cobrem: ingestão de leituras pela API (autenticação, validação, rate limiting), episódios de alerta, irrigação, recomendações, diagnóstico do solo, faixas por cultura, busca e paginação das listagens, isolamento entre usuários, cadastro de propriedade e os serviços do Open-Meteo.

## 📦 Deploy (Produção)

```bash
# .env
APP_ENV=production
APP_DEBUG=false

# Comandos
composer install --no-dev --optimize-autoloader
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

A fila de e-mails precisa de um worker rodando (`php artisan queue:work`).

## 🗓️ Entregas do TCC II (Agosto a Dezembro 2026)

- [x] Estrutura Laravel MVC em camadas (Entity / Model / Service / Controller)
- [x] CRUDs de propriedades, lavouras, insumos e sensores
- [x] API REST de ingestão de leituras, autenticada por token e com rate limiting
- [x] Firmware ESP32 com leitura RS485/Modbus, envio das leituras, controle da irrigação e Deep Sleep
- [x] Validação com o hardware físico, com leituras reais em produção
- [x] Dashboard com dados reais por Propriedade → Lavoura, avaliados contra as faixas ideais
- [x] Faixas ideais por lavoura com valores sugeridos por cultura
- [x] Irrigação automática com histórico
- [x] Alertas por episódio com severidade e notificação por e-mail
- [x] Recomendações por regras condicionais
- [x] Relatórios com diagnóstico do solo e exportação em PDF
- [x] Relatório de insumos com PDF e envio por e-mail
- [x] Previsão do tempo (Open-Meteo) e cadastro de propriedade com mapa
- [x] Recuperação de senha, limite de tentativas no login e isolamento multi-tenant
- [x] Testes automatizados (105 testes)

### Trabalhos Futuros
- Comunicação criptografada (HTTPS/TLS) entre o ESP32 e o servidor
- Acionamento manual da válvula pelo dashboard chegando ao dispositivo
- Machine Learning para predição de irrigação, usando o histórico de leituras e a previsão do tempo
- App mobile e notificações push
- Conectividade LoRaWAN para áreas sem Wi-Fi

## 📚 Documentação Acadêmica

Este projeto é parte do **Trabalho de Conclusão de Curso (TCC)** do curso de **Engenharia de Software**.

- **TCC I (Nota: 9.7):** Fundamentação teórica, requisitos, modelagem e arquitetura
- **TCC II:** Implementação prática do sistema completo

**Orientador:** George Mendes Marra
**Aluno:** Iury de Andrade Hilário
**Ano:** 2026

## 📞 Contato

- **GitHub:** [@IuryHilario](https://github.com/IuryHilario)

---

**Status**: Concluído — TCC II (2026)
