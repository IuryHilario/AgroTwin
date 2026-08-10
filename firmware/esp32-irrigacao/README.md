# Firmware ESP32 — Controlador da Válvula de Irrigação

Liga/desliga um relé (que aciona a válvula solenoide) com base na decisão de
irrigação tomada pelo AgroTwin (`App\Services\IrrigacaoService`), que compara
a última leitura de umidade do solo com os limites configurados para a
lavoura. Este firmware **não decide nada sozinho** — só consulta o servidor e
obedece.

## Ligação (wiring)

```
ESP32                Módulo Relé              Válvula Solenoide
  3.3V/5V ---------- VCC
  GND -------------- GND ---------------------  GND (fonte 12V da válvula)
  GPIO26 (RELE_PIN)-- IN
                      COM/NO -------------------  Alimentação da válvula (12V)
```

- A válvula solenoide normalmente precisa de 12V — o relé só chaveia essa
  alimentação, ele não alimenta a válvula sozinho.
- A maioria dos módulos relé de 1 canal aciona em nível baixo (`LOW` liga,
  `HIGH` desliga). Se a válvula fizer o oposto do esperado ao testar, mude
  `RELE_ATIVO_EM_LOW` para `false` em `config.h`.

## Passo a passo de configuração

1. No AgroTwin, abra a lavoura que terá irrigação automática → **Monitorar** →
   seção "Irrigação". Copie o **ID da lavoura** (mostrado na URL) e o
   **Token de irrigação**.
2. Configure os **limites de umidade do solo** dessa lavoura (mínimo e
   máximo) em "Configurar Limites" — é esse limite que o servidor usa para
   decidir quando irrigar.
3. Copie `include/config.example.h` para `include/config.h` e preencha
   Wi-Fi, `API_BASE_URL`, `ID_LAVOURA` e `TOKEN_IRRIGACAO`.
4. Compile e grave no ESP32 via PlatformIO.
5. Abra o Monitor Serial (115200 baud) — a cada 30s (configurável) o
   dispositivo consulta o servidor e mostra se a válvula está aberta ou
   fechada.

## Como a decisão é tomada

- Toda vez que uma nova leitura de umidade do solo chega pela API de
  ingestão (do outro firmware, `esp32-solo-7em1`), o servidor verifica se o
  valor está abaixo do limite mínimo configurado.
- Se estiver, a irrigação é ligada automaticamente e fica registrada no
  histórico da lavoura (`historico_irrigacao`), com o motivo.
- Quando uma leitura seguinte mostrar a umidade de volta dentro da faixa, o
  servidor desliga a irrigação sozinho — não precisa de intervenção manual.
- O agricultor também pode ligar/desligar manualmente pela tela "Monitorar"
  da lavoura no AgroTwin; este firmware obedece os dois casos da mesma forma,
  já que só consulta o resultado final (`irrigar: true/false`).
