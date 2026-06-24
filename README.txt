================================================================================
                              MEDSTOCK
        Sistema de Gestão de Inventário de Equipamentos Médicos
================================================================================

NOME DO PROJETO:    MedStock
NOME DO ESTUDANTE:  Gabriela Sofia Teixeira Arantes
NÚMERO DO ESTUDANTE: 1241094
UNIDADE CURRICULAR: Sistemas de Informação e Bases de Dados Aplicados à Saúde
ANO LETIVO:         2025/2026

================================================================================
INSTRUÇÕES DE INSTALAÇÃO E EXECUÇÃO
================================================================================

PRÉ-REQUISITOS:
  - Servidor web com suporte a PHP (versão 8.0 ou superior)
  - Base de dados MySQL/MariaDB
  - Laragon (ambiente local) ou servidor ISEP

PASSOS DE INSTALAÇÃO:

1. Copiar a pasta do projeto para o diretório do servidor web:
      Caminho: .../www/sibdas/1241094/medstock/

2. Importar a base de dados:
   - Aceder ao HeidiSQL ou phpMyAdmin
   - Criar a base de dados: db1241094
   - Importar o ficheiro: base de dados/CreateTables1241094.sql
   - Importar o ficheiro: base de dados/Inserts1241094.sql

3. Configurar a ligação à base de dados:
   - Editar o ficheiro: config/config.php
   - Preencher os seguintes campos:
       MYSQL_HOST:     vsgate-s1.dei.isep.ipp.pt
       MYSQL_PORT:     10464
       MYSQL_DATABASE: db1241094
       MYSQL_USERNAME: 1241094
       MYSQL_PASSWORD: arantes_094

4. Aceder à aplicação no browser:
      URL: http://vsgate-s1.dei.isep.ipp.pt/sibdas/1241094/medstock/public/index.php

================================================================================
INSTRUÇÕES PARA REALIZAÇÃO DOS PRINCIPAIS TESTES
================================================================================

1. ÁREA PÚBLICA
   - Aceder ao URL acima indicado
   - Navegar pelas secções: Início, Sobre, Funcionalidades, Contacto
   - Preencher e submeter o formulário de contacto

2. AUTENTICAÇÃO
   - Clicar em "Iniciar Sessão" na barra de navegação
   - Introduzir as credenciais de um dos perfis listados abaixo

3. EQUIPAMENTOS
   - Inserir um novo equipamento (preencher todos os separadores)
   - Editar um equipamento existente
   - Visualizar os detalhes de um equipamento
   - Desativar um equipamento e verificar que fica marcado como inativo
   - Reativar o equipamento desativado

4. FORNECEDORES / LOCALIZAÇÕES
   - Inserir, editar e desativar registos
   - Verificar que não é possível desativar se tiver equipamentos ativos associados

5. GARANTIAS E CONTRATOS
   - Inserir uma garantia com data de início futura (deve ser rejeitada)
   - Inserir com data de fim anterior ao início (deve ser rejeitada)

6. DOCUMENTAÇÃO
   - Inserir um documento com ficheiro de extensão inválida (deve ser rejeitada)
   - Inserir com nome contendo números (deve ser rejeitado)

7. DASHBOARD
   - Verificar os indicadores: total de equipamentos, ativos, em manutenção, inativos
   - Verificar alertas de garantias a expirar nos próximos 30 dias

8. EXPORTAÇÃO
   - Exportar a listagem de equipamentos em CSV, JSON e PDF

================================================================================
CREDENCIAIS DE ACESSO
================================================================================

PERFIL: Administrador
  Utilizador: admin@medstock.pt
  Password:   admin123
  Acesso:     Total — todos os módulos disponíveis

PERFIL: Técnico
  Utilizador: tecnico1@medstock.pt
  Password:   tecnico123
  Acesso:     Dashboard, Equipamentos, Localização, Fornecedores,
              Garantias/Contratos, Documentação

PERFIL: Profissional de Saúde
  Utilizador: saude1@medstock.pt
  Password:   saude123
  Acesso:     Dashboard e Equipamentos (apenas consulta)

================================================================================
INFORMAÇÕES ADICIONAIS
================================================================================

- A aplicação utiliza soft-delete: os registos desativados não são eliminados
  permanentemente da base de dados, podendo ser reativados posteriormente.

- Os URLs com identificadores de registos são cifrados com AES para
  impedir a manipulação direta de parâmetros.

- As passwords dos utilizadores são armazenadas com hash seguro (bcrypt).

- A base de dados está normalizada até à Terceira Forma Normal (3NF).

- Estrutura do projeto disponível em: /sibdas/1241094/medstock/

================================================================================