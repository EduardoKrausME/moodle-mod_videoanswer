# mod_videoanswer

Video Answer é uma atividade Moodle para respostas curtas em vídeo gravadas diretamente com a câmera do computador ou
do celular do estudante.

## Como funciona

O professor escreve uma pergunta ou proposta, por exemplo “Explique este conceito em até 60 segundos”, e define o limite
de gravação. O estudante grava no próprio navegador, assiste ao resultado antes de enviar e, quando permitido, pode
substituir a gravação por uma nova tentativa.

A atividade mantém uma submissão atual por estudante e armazena o vídeo pela File API do Moodle, com entrega autenticada
e verificação de permissões.

## Recursos

- limites de gravação de 30, 60 ou 120 segundos;
- captura de câmera e microfone diretamente no navegador;
- pré-visualização antes do envio;
- opção de regravar ou substituir a resposta;
- relatório com estudante, duração, horário de envio e reprodução do vídeo;
- armazenamento protegido pela File API;
- integração com a Privacy API do Moodle.
