# AgroTwin - Plataforma Inteligente de Gestão Agrícola

## 📋 Descrição

**AgroTwin** é uma plataforma inteligente para gestão integrada de propriedades agrícolas, desenvolvida com Laravel 12 e Blade Templates. O sistema centraliza informações sobre propriedades, lavouras, insumos, sensores IoT e recomendações baseadas em regras condicionais para otimizar a produção agrícola, com foco em hortaliças para pequenos e médios produtores.

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
- Coleta de umidade, NPK (N, P, K), pH, temperatura e condutividade elétrica via sensor NPKPHCTH-S 7-em-1
- Transmissão em tempo real via API REST (`POST /api/sensores/{id}/leituras`), autenticada por token de dispositivo e com rate limiting — a proposta original do TCC previa MQTT/broker Mosquitto; foi simplificado para REST para adiantar a implementação antes do hardware chegar (ver Roadmap)
- Armazenamento das leituras no MariaDB — a proposta original previa InfluxDB para as séries temporais; o `SensorReadingService` isola esse ponto para facilitar a troca depois
- Intervalo de leitura configurável pelo agricultor (por Lavoura), buscado pelo firmware do servidor no boot

### 2. **Irrigação Inteligente**
- Acionamento automático da válvula solenoide via regras condicionais, comparando a umidade do solo com os limites configurados
- Controle manual pelo dashboard (iniciar/parar a qualquer momento)
- Encerramento automático quando o parâmetro volta ao nível adequado
- Histórico completo de irrigações (automática e manual), com início, fim e motivo
- Firmware dedicado (`firmware/esp32-irrigacao/`) que só consulta o servidor e aciona o relé — a decisão fica toda no backend

### 3. **Gestão de Propriedades e Lavouras**
- Cadastro de propriedades com tipo de solo, localização (texto) e área
- Gestão de lavouras com datas de plantio/colheita e status
- Configuração de limites min/max por parâmetro e por lavoura

### 4. **Controle de Insumos**
- Cadastro de fertilizantes, defensivos, sementes e adubos
- Controle de estoque com entrada/saída
- Histórico de movimentações e registro de aplicação

### 5. **Sistema de Alertas**
- Alertas quando um parâmetro sai da faixa configurada (umidade, pH, temperatura, NPK, condutividade)
- Marcar como lido/não lido, notificação por e-mail (fila assíncrona) opcional via Configurações
- ⚠️ Os níveis de severidade (info/warning/critical) existem no modelo de dados, mas hoje todo alerta é gerado como "warning" — a classificação por gravidade ainda não foi implementada

### 6. **Regras Condicionais e IA**
- Regras baseadas em múltiplos parâmetros (umidade, NPK, pH, temperatura, condutividade) para decidir alertas, recomendações e irrigação
- Recomendações geradas por regras condicionais e salvas no histórico da lavoura
- Preparado para integração futura de Machine Learning — etapa planejada para o fim do TCC II, ainda não iniciada

### 7. **Dashboard**
- Seletor de Propriedade → Lavoura, com indicadores da lavoura selecionada (evita misturar sensores de lavouras diferentes)
- Gráficos históricos, status dos sensores, alertas ativos e recomendações
- Tela de Perfil (dados da conta, troca de senha) e Configurações (notificação por e-mail, propriedade padrão do dashboard)
- Interface simples e intuitiva, com tema claro/escuro

### 8. **Dados Climáticos**
- Integração com a API OpenWeatherMap implementada no backend (`WeatherController`)
- ⚠️ Ainda não está exposta em nenhuma tela do dashboard — endpoint pronto, front-end pendente

## 🛠️ Stack Tecnológico

### Hardware (IoT)
- **ESP32 DevKit V1** - Microcontrolador com Wi-Fi nativo
- **Sensor NPKPHCTH-S 7-em-1** - Umidade, NPK, pH, condutividade, temperatura (RS485/Modbus)
- **Válvula Solenoide 12V** - Acionamento da irrigação
- **Módulo Relé 1 canal** - Interface ESP32 ↔ válvula
- **Conversor MAX485** - RS485 para TTL (comunicação sensor ↔ ESP32)

### Comunicação
- **API REST (HTTP/JSON)** - ESP32 → Laravel, autenticada por token de dispositivo (em uso hoje)
- **Wi-Fi 802.11 b/g/n** - Conectividade
- **RS485/Modbus RTU** - Comunicação sensor ↔ ESP32
- **MQTT + TLS/SSL** - Planejado na proposta original, ainda não implementado (ver Roadmap)

### Backend
- **Laravel 12.x** - Framework PHP (MVC)
- **PHP 8.2+** - Linguagem principal
- **API REST** - Ingestão de leituras e consulta de estado de irrigação
- Autenticação própria por e-mail/senha (sem Sanctum/Passport)

### Bancos de Dados
- **MariaDB** - Todos os dados hoje: usuários, propriedades, lavouras, insumos, sensores, leituras, alertas, recomendações, histórico de irrigação
- **InfluxDB** - Planejado na proposta original para séries temporais dos sensores; ainda não integrado

### Frontend
- **Blade Templates** - Templates PHP nativo do Laravel
- **Tailwind CSS 4.x** - Styling responsivo, com tema claro/escuro
- **Alpine.js 3** - Interatividade (sidebar, modais)
- **Chart.js** - Gráficos históricos
- **Vite 6.x** - Build tool
- **Axios** - Cliente HTTP (dependência do scaffolding padrão do Laravel)

### DevOps
- **PHPUnit 11.x** - Testes automatizados (`php artisan test`)
- **Laravel Pint** - Code formatting

### Integrações
- **OpenWeatherMap API** - Implementada no backend, não exposta na interface ainda

## 🏗️ Arquitetura IoT em Camadas

Estrutura conceitual em 4 camadas, conforme definida na proposta do TCC. O que já está implementado hoje aparece indicado; o restante é o que falta para bater 100% com a proposta original.

```
┌─────────────────────────────────────────────────┐
│  CAMADA 4 — APLICAÇÃO                           │
│  Dashboard Laravel (MVC) · Login · Gráficos     │
│  Alertas · Recomendações · Perfil/Configurações │  ✅ Implementado
├──────────────── API REST (JSON) ────────────────┤
│  CAMADA 3 — PROCESSAMENTO / SERVIDOR            │
│  Backend Laravel · MariaDB (InfluxDB pendente)  │  ✅ Backend/MariaDB
│  Regras Condicionais · Broker MQTT (pendente)   │  ❌ MQTT pendente
├──────────────── HTTP hoje / MQTT planejado ─────┤
│  CAMADA 2 — REDE / COMUNICAÇÃO                  │
│  ESP32 (Gateway) · Wi-Fi · Pré-processamento    │  ✅ Implementado
│  Deep Sleep (pendente)                          │  ❌ Deep Sleep pendente
├──────────────── RS485 ──────────────────────────┤
│  CAMADA 1 — PERCEPÇÃO                           │
│  Sensor 7-em-1 · Válvula Solenoide              │  ✅ Firmware pronto,
│                                                   │     aguardando hardware físico
└─────────────────────────────────────────────────┘
```

## 📁 Estrutura de Pastas

```
AgroTwin/
├── app/
│   ├── Entity/               # Constantes de tabela/fillable/casts + queries estáticas
│   ├── Enums/                # TipoSensor, TipoStatus, TipoInsumo, etc.
│   ├── Http/
│   │   ├── Controllers/      # Controllers (shell) + subpastas por módulo (Tela/Crud)
│   │   └── Requests/         # Form Requests (validação)
│   ├── Mail/                 # Mailables (alerta por e-mail)
│   ├── Models/                # Modelos Eloquent + traits Core/Dto/Insert/Update
│   ├── Services/               # Regras de negócio (leituras, alertas, irrigação, recomendações)
│   ├── Traits/                 # Traits reutilizáveis
│   └── Providers/               # Service Providers
├── database/
│   └── migrations/custom/       # Migrations do projeto
├── firmware/
│   ├── esp32-solo-7em1/         # Firmware do sensor de solo (PlatformIO)
│   └── esp32-irrigacao/         # Firmware da válvula/relé (PlatformIO)
├── routes/
│   ├── web.php                  # Rotas web
│   ├── api.php                  # Rotas API REST
│   └── rotasWeb/                # Rotas separadas por módulo
├── resources/
│   ├── views/                   # Templates Blade, um diretório por módulo
│   ├── css/                     # Tailwind
│   └── js/                      # Alpine/módulos JS por tela
├── config/                      # Configurações
├── storage/                     # Logs, uploads
├── tests/                       # Testes automatizados
└── public/                      # Assets públicos
```

## 🔄 Fluxo do Sistema

```
Sensor 7-em-1 coleta dados do solo
         │
         │ RS485/Modbus
         ▼
ESP32 recebe e envia
         │
         │ HTTP/JSON (token do dispositivo)
         │ [MQTT via broker é o planejado na proposta, ainda não implementado]
         ▼
Backend Laravel processa
         │
         ▼
MariaDB armazena
(leituras, alertas, recomendações, histórico de irrigação)
[InfluxDB para as leituras é o planejado na proposta, ainda não implementado]
         │
         ▼
Regras condicionais avaliam
    │
    ├── Umidade abaixo do limite? → Liga a irrigação (histórico registrado)
    │                                 ESP32 da válvula consulta e aciona o relé
    │
    └── Parâmetro fora da faixa? → Gera alerta e recomendação
                                     E-mail opcional via fila
                                     │
                                     ▼
                           Dashboard exibe tudo
                           por Propriedade → Lavoura
```

## 🚀 Como Executar

### Pré-requisitos
- PHP 8.2+
- Composer
- Node.js 18+ e NPM
- MariaDB
- PlatformIO (ou extensão no VS Code) para o firmware ESP32

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

4. **Configure o banco de dados no .env**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=agrotwin
   DB_USERNAME=user_agrotwin
   DB_PASSWORD=senha_segura

   # Opcional — notificação de alertas por e-mail
   MAIL_MAILER=log
   QUEUE_CONNECTION=database

   # Opcional — dados climáticos (backend pronto, sem tela ainda)
   OPENWEATHER_API_KEY=sua_chave_api
   ```

5. **Execute migrations e inicie**
   ```bash
   php artisan migrate
   composer run dev    # sobe servidor + fila + logs + vite juntos
   ```

6. **Firmware ESP32** (quando o hardware estiver em mãos)
   ```
   1. Abra firmware/esp32-solo-7em1/ (ou esp32-irrigacao/) no VS Code com PlatformIO
   2. Copie include/config.example.h para include/config.h e preencha Wi-Fi + tokens
   3. "PlatformIO: Upload" com o ESP32 conectado por USB
   ```
   Detalhes completos (esquema de ligação, mapeamento de registradores) no README de cada firmware.

Acesse: `http://localhost:8000`

## 🧪 Testes

```bash
php artisan test              # Testes automatizados (Feature)
php artisan test --coverage   # Com cobertura
```

Cobrem hoje: ingestão de leituras (autenticação, validação, rate limiting), geração de alertas, decisão de irrigação e recomendações.

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
- [x] CRUDs completos (propriedades, lavouras, insumos, sensores)
- [x] API REST de ingestão de leituras (autenticada, com rate limiting)
- [x] Dashboard com gráficos e dados reais, por Propriedade/Lavoura
- [x] Regras condicionais de irrigação (automática e manual) + histórico
- [x] Sistema de alertas e recomendações (persistidas)
- [x] Notificação de alertas por e-mail (fila)
- [x] Firmware ESP32 (sensor e válvula) — aguardando hardware físico para teste real
- [x] Testes automatizados
- [ ] Integração MQTT (ESP32 → Mosquitto → Laravel) — hoje é REST direto
- [ ] Integração InfluxDB para séries temporais — hoje é MariaDB
- [ ] TLS/SSL na comunicação do dispositivo
- [ ] Deep Sleep no ESP32
- [ ] Classificação de severidade dos alertas (hoje sempre "warning")
- [ ] Teste formal de tempo de resposta do dashboard (<5s, requisito da proposta)
- [ ] Machine Learning (predição de irrigação)
- [ ] Validação em cenário real com o hardware físico

### Futuro
- [ ] App mobile (React Native)
- [ ] Conectividade LoRaWAN
- [ ] Notificações push
- [ ] Tela para os dados climáticos (backend já pronto)

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

**Status**: Em Desenvolvimento Ativo
