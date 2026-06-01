# AgroTwin - Plataforma Inteligente de Gestão Agrícola

## 📋 Descrição

**AgroTwin** é uma plataforma inteligente para gestão integrada de propriedades agrícolas, desenvolvida com Laravel 12 e Blade Templates. O sistema centraliza informações sobre propriedades, lavouras, insumos, sensores IoT e recomendações baseadas em regras condicionais e IA para otimizar a produção agrícola, com foco em hortaliças para pequenos e médios produtores.

## 🎯 Objetivo

Fornecer aos agricultores uma solução completa, acessível e de baixo custo para:
- Monitorar umidade, NPK, pH e temperatura do solo em tempo real
- Automatizar a irrigação com base em regras condicionais
- Gerenciar propriedades, lavouras e insumos
- Receber alertas em situações críticas
- Acompanhar histórico de leituras e irrigações

## 👥 Público-Alvo

- Pequenos e médios produtores de hortaliças
- Gestores de propriedades agrícolas
- Agrônomos e consultores agrícolas

## ✨ Funcionalidades Principais

### 1. **Monitoramento IoT do Solo**
- Coleta de umidade, NPK (N, P, K), pH e temperatura via sensor NPKPHCTH-S 7-em-1
- Transmissão em tempo real via MQTT (broker Mosquitto)
- Armazenamento de séries temporais no InfluxDB
- Intervalos de leitura configuráveis pelo agricultor

### 2. **Irrigação Inteligente**
- Acionamento automático da válvula solenoide via regras condicionais
- Controle manual pelo dashboard
- Encerramento automático quando parâmetros atingem nível adequado
- Histórico completo de irrigações (automática e manual)

### 3. **Gestão de Propriedades e Lavouras**
- Cadastro de propriedades com tipo de solo, localização e área
- Gestão de lavouras com datas de plantio/colheita e status
- Configuração de limites min/max por parâmetro e por cultura
- Geolocalização das propriedades

### 4. **Controle de Insumos**
- Cadastro de fertilizantes, defensivos, sementes e adubos
- Controle de estoque com entrada/saída
- Alertas de estoque mínimo e validade
- Histórico de movimentações

### 5. **Sistema de Alertas**
- Alertas críticos: umidade baixa, pH fora da faixa, temperatura extrema, NPK insuficiente
- Níveis de severidade: info, warning, critical
- Marcar como lido/não lido
- Notificações no dashboard

### 6. **Regras Condicionais e IA**
- Regras baseadas em múltiplos parâmetros (umidade + NPK + pH + temperatura)
- Tomada de decisão automática para irrigação e alertas
- Preparado para integração futura de Machine Learning (predição por regressão)

### 7. **Dashboard**
- Status do solo em tempo real (cards por parâmetro)
- Gráficos históricos
- Resumo de alertas e irrigações
- Interface simples e intuitiva para agricultores

### 8. **Dados Climáticos**
- Integração com API OpenWeatherMap
- Previsão de clima por localização
- Dados de temperatura, umidade e vento

## 🛠️ Stack Tecnológico

### Hardware (IoT)
- **ESP32 DevKit V1** - Microcontrolador com Wi-Fi nativo (dual-core 240MHz, Deep Sleep ~5μA)
- **Sensor NPKPHCTH-S 7-em-1** - Umidade, NPK, pH, condutividade, temperatura (RS485)
- **Sensor Capacitivo v1.2** - Umidade do solo (alternativa individual)
- **Válvula Solenoide 12V** - Acionamento da irrigação
- **Módulo Relé 1 canal** - Interface ESP32 ↔ válvula
- **Conversor MAX485** - RS485 para TTL (comunicação sensor ↔ ESP32)

### Comunicação
- **MQTT** - Protocolo leve para IoT (publicar/assinar)
- **Mosquitto** - Broker MQTT
- **Wi-Fi 802.11 b/g/n** - Conectividade (LoRaWAN como expansão futura)
- **RS485** - Comunicação sensor ↔ ESP32
- **TLS/SSL** - Criptografia sobre MQTT

### Backend
- **Laravel 12.x** - Framework PHP (MVC)
- **PHP 8.2+** - Linguagem principal
- **API REST** - Comunicação backend ↔ dashboard
- **Laravel Sanctum** - Autenticação

### Bancos de Dados
- **InfluxDB** - Séries temporais dos sensores (leituras contínuas)
- **MariaDB** - Dados relacionais (usuários, propriedades, lavouras, insumos, alertas)

### Frontend
- **Blade Templates** - Templates PHP nativo do Laravel
- **Tailwind CSS 4.x** - Styling responsivo
- **Chart.js** - Gráficos históricos
- **Vite 6.x** - Build tool
- **Axios** - Cliente HTTP

### DevOps
- **GitHub Actions** - CI/CD
- **PHPUnit 11.x** - Testes unitários
- **Laravel Pint** - Code formatting

### Integrações
- **OpenWeatherMap API** - Dados climáticos

## 🏗️ Arquitetura IoT em Camadas

```
┌─────────────────────────────────────────────────┐
│  CAMADA 4 — APLICAÇÃO                           │
│  Dashboard Laravel (MVC) · Login · Gráficos     │
│  Alertas · Gerenciamento · API REST             │
├──────────────── API REST (JSON) ────────────────┤
│  CAMADA 3 — PROCESSAMENTO / SERVIDOR            │
│  Backend Laravel · InfluxDB · MariaDB           │
│  Broker MQTT · Regras Condicionais · ML (futuro)│
├──────────────── MQTT (Wi-Fi) ───────────────────┤
│  CAMADA 2 — REDE / COMUNICAÇÃO                  │
│  ESP32 (Gateway) · Wi-Fi · Pré-processamento    │
│  Deep Sleep · Expansão futura: LoRaWAN          │
├──────────────── RS485 ──────────────────────────┤
│  CAMADA 1 — PERCEPÇÃO                           │
│  Sensor 7-em-1 · Capacitivo v1.2                │
│  Válvula Solenoide · Calibração por solo        │
└─────────────────────────────────────────────────┘
```

## 📁 Estrutura de Pastas

```
AgroTwin/
├── app/
│   ├── Entity/              # Entidades de domínio
│   ├── Enums/               # Enumerações (Status, Tipo)
│   ├── Http/
│   │   ├── Controllers/     # Controllers
│   │   └── Requests/        # Form Requests (validação)
│   ├── Models/              # Modelos Eloquent
│   ├── Services/            # Serviços de negócio
│   ├── Traits/              # Traits reutilizáveis
│   ├── Utils/               # Utilidades
│   └── Providers/           # Service Providers
├── database/
│   ├── migrations/          # Migrations
│   └── seeders/             # Seeders
├── hardware/                # Código ESP32 (Arduino IDE)
│   ├── src/                 # Código-fonte do firmware
│   ├── lib/                 # Bibliotecas
│   └── docs/                # Esquemas do circuito
├── routes/
│   ├── web.php              # Rotas web
│   ├── api.php              # Rotas API REST
│   └── rotasWeb/            # Rotas separadas por módulo
├── resources/
│   ├── views/               # Templates Blade
│   ├── css/                 # Estilos
│   └── js/                  # Scripts
├── config/                  # Configurações
├── storage/                 # Logs, uploads
├── tests/                   # Testes automatizados
├── docs/                    # Documentação, diagramas
└── public/                  # Assets públicos
```

## 🔄 Fluxo do Sistema

```
Sensor 7-em-1 coleta dados do solo
         │
         │ RS485
         ▼
ESP32 recebe e pré-processa
         │
         │ MQTT via Wi-Fi
         ▼
Broker Mosquitto recebe
         │
         ▼
Backend Laravel processa
         │
    ┌────┴────┐
    ▼         ▼
InfluxDB   MariaDB
(sensores) (usuários)
    │
    ▼
Regras condicionais avaliam
    │
    ├── Necessita irrigar? → Aciona válvula solenoide
    │
    └── Situação crítica? → Envia alerta ao agricultor
                              │
                              ▼
                    Dashboard exibe tudo
                    via API REST (JSON)
```

## 🚀 Como Executar

### Pré-requisitos
- PHP 8.2+
- Composer
- Node.js 18+ e NPM
- MariaDB
- InfluxDB 2.x
- Mosquitto (broker MQTT)
- Arduino IDE (para firmware ESP32)

### Instalação

1. **Clone o repositório**
   ```bash
   git clone https://github.com/IuryHilario/AgroTwin.git
   cd AgroTwin
   ```

2. **Instale dependências**
   ```bash
   composer install
   npm install
   ```

3. **Configure o ambiente**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure os bancos de dados no .env**
   ```env
   # MariaDB (dados relacionais)
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=agrotwin
   DB_USERNAME=user_agrotwin
   DB_PASSWORD=senha_segura

   # InfluxDB (séries temporais)
   INFLUXDB_URL=http://localhost:8086
   INFLUXDB_TOKEN=seu_token
   INFLUXDB_BUCKET=agrotwin_sensores
   INFLUXDB_ORG=agrotwin

   # MQTT
   MQTT_HOST=127.0.0.1
   MQTT_PORT=1883
   MQTT_USERNAME=
   MQTT_PASSWORD=

   # OpenWeather
   OPENWEATHER_API_KEY=sua_chave_api
   ```

5. **Execute migrations e inicie**
   ```bash
   php artisan migrate
   php artisan serve    # Terminal 1
   npm run dev          # Terminal 2
   ```

6. **Firmware ESP32** (quando hardware estiver pronto)
   ```
   1. Abra Arduino IDE
   2. Abra hardware/src/main.ino
   3. Configure Wi-Fi e MQTT no código
   4. Faça upload para o ESP32
   ```

Acesse: `http://localhost:8000`

## 🧪 Testes

```bash
php artisan test              # Testes unitários
php artisan test --coverage   # Com cobertura
```

## 📦 Deploy (Produção)

```bash
APP_ENV=production
APP_DEBUG=false

npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
```

## 🗓️ Roadmap

### TCC II — Agosto a Dezembro 2026
- [x] Estrutura Laravel com MVC
- [x] Migrations do banco de dados
- [x] CRUDs básicos (propriedades, lavouras, insumos)
- [ ] Integração MQTT (ESP32 → Mosquitto → Laravel)
- [ ] Dashboard com gráficos em tempo real
- [ ] Regras condicionais de irrigação
- [ ] Acionamento de válvula solenoide via MQTT
- [ ] Sistema de alertas
- [ ] Integração InfluxDB para séries temporais
- [ ] Machine Learning (predição de irrigação)
- [ ] Validação em cenário real/simulado

### Futuro
- [ ] App mobile (React Native)
- [ ] Conectividade LoRaWAN
- [ ] Notificações push
- [ ] Marketplace de insumos

## 📚 Documentação Acadêmica

Este projeto é parte do **Trabalho de Conclusão de Curso (TCC)** do curso de **Engenharia de Software**.

- **TCC I (Nota N1: 10 | Nota N2: 9.7):** Fundamentação teórica, requisitos, modelagem e arquitetura
- **TCC II:** Implementação prática do sistema completo

**Orientador:** George Mendes Marra
**Aluno:** Iury de Andrade Hilário
**Ano:** 2026

## 📄 Licença

Este projeto está licenciado sob a [MIT License](LICENSE).

## 📞 Contato

- **GitHub:** [@IuryHilario](https://github.com/IuryHilario)

---

**Versão**: 1.0.0
**Última Atualização**: 31 de Maio de 2026
**Status**: Em Desenvolvimento Ativo