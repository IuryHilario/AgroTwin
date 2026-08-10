# Firmware ESP32 — Sensor de Solo 7 em 1 (RS485/Modbus)

Lê o sensor de solo 7 em 1 (umidade, temperatura, condutividade/EC, pH,
nitrogênio, fósforo, potássio) via Modbus RTU sobre RS485 e envia cada
parâmetro para a API de ingestão do AgroTwin (`POST /api/sensores/{id}/leituras`).

## Ligação (wiring)

O sensor se comunica em RS485, então é preciso um conversor RS485 <-> TTL
(módulo MAX485) entre ele e o ESP32:

```
Sensor RS485        Módulo MAX485        ESP32
   A/A+  ------------  A
   B/B-  ------------  B
                        RO  ------------  GPIO16 (RX2)
                        DI  ------------  GPIO17 (TX2)
                        DE  ---+
                        RE  ---+---------  GPIO4
                        VCC ------------  3.3V ou 5V (conforme o módulo)
                        GND ------------  GND (comum com o ESP32 e o sensor)
   VCC (alimentação) ---------------------  fonte externa 12V (ver manual do sensor)
   GND -----------------------------------  GND comum
```

- `DE` e `RE` do MAX485 ficam ligados **juntos** no mesmo pino do ESP32 (o
  firmware alterna esse pino automaticamente entre transmitir e receber).
- O sensor normalmente precisa de alimentação externa de 12V — **não** ligue
  o VCC dele direto no ESP32. Use uma fonte 12V separada com o GND comum a
  todo o circuito.
- Os pinos GPIO16/17/4 usados aqui são só a configuração padrão — dá pra
  trocar em `include/config.h` se preferir outros pinos.

## Mapeamento de registradores Modbus

O firmware assume o mapeamento mais comum para esse tipo de sensor (registrador
inicial `0x0000`, function code `0x03`, 7 registradores sequenciais):

| Registrador | Parâmetro       | Escala          |
|-------------|-----------------|-----------------|
| 0x0000      | Umidade         | valor / 10 (%)  |
| 0x0001      | Temperatura     | valor / 10 (°C) |
| 0x0002      | Condutividade   | valor (µS/cm)   |
| 0x0003      | pH              | valor / 10      |
| 0x0004      | Nitrogênio      | valor (mg/kg)   |
| 0x0005      | Fósforo         | valor (mg/kg)   |
| 0x0006      | Potássio        | valor (mg/kg)   |

**Se as leituras vierem com valores absurdos ou zerados**, confira o
manual/QR code que acompanha o sensor — alguns lotes usam endereços,
slave ID (padrão `1`) ou baud rate (padrão `9600`) diferentes. Ajuste as
constantes `REG_*` em `src/main.cpp` e `MODBUS_SLAVE_ID`/`MODBUS_BAUD_RATE`
em `include/config.h` conforme necessário.

## Passo a passo de configuração

1. **No AgroTwin**, garanta que já existe uma Lavoura para associar os
   sensores.
2. Cadastre um **Sensor** para cada parâmetro que o seu sensor físico mede
   (Umidade, Temperatura, Condutividade Elétrica, pH, Nitrogênio, Fósforo,
   Potássio), todos associados à mesma Lavoura. Se não for usar algum
   parâmetro, não precisa cadastrar.
3. Para cada Sensor cadastrado, abra **Detalhar** → seção "Integração com o
   Dispositivo (ESP32)" e copie a **URL de Envio** (o ID do sensor está nela)
   e o **Token**.
4. Copie `include/config.example.h` para `include/config.h` e preencha:
   - `WIFI_SSID` / `WIFI_PASSWORD`
   - `API_BASE_URL` (o host:porta onde o Laravel está acessível pela rede
     local — não use `localhost`, use o IP da máquina)
   - Para cada parâmetro que você cadastrou, o `ID_*` e `TOKEN_*`
     correspondentes. Deixe `TOKEN_*` vazio (`""`) para os que não usar.
5. Instale a extensão **PlatformIO** no VS Code, abra esta pasta
   (`firmware/esp32-solo-7em1`) e use "PlatformIO: Upload" com o ESP32
   conectado por USB.
6. Abra o Monitor Serial (115200 baud) para acompanhar as leituras e o
   resultado do envio HTTP de cada uma.
7. Volte ao dashboard do AgroTwin — as leituras devem aparecer nos
   indicadores e, se os limites estiverem configurados na Lavoura, alertas,
   recomendações e (para umidade do solo) a irrigação automática passam a
   funcionar a partir dos dados reais.

## Intervalo de leitura configurável

O intervalo entre leituras não precisa ficar fixo no firmware: o agricultor
configura, por lavoura, quantos minutos entre uma leitura e outra na tela de
editar Lavoura (campo "Intervalo de leitura dos sensores"). No `setup()`, o
firmware busca esse valor em `GET /api/sensores/{id}/config` (usando o
sensor de umidade como referência) e passa a usá-lo em vez do
`READ_INTERVAL_MS` padrão de `config.h`. Se a busca falhar (servidor fora do
ar, sensor de umidade não configurado, etc.), o valor padrão é mantido.

## Simulação enquanto o hardware não chegava

O comando `php artisan sensores:simular-leitura` (agendado a cada 5 minutos em
ambiente local) gera leituras plausíveis para testar o sistema sem o sensor
físico. Ele não atrapalha o firmware real — são só chamadas à mesma API de
ingestão. Depois que o sensor físico estiver funcionando, você pode remover o
agendamento em `routes/console.php` se não quiser mais dados simulados
misturados aos reais.
