-- --------------------------------------------------------
-- Anfitrião:                    vsgate-s1.dei.isep.ipp.pt
-- Versão do servidor:           8.0.45 - MySQL Community Server - GPL
-- SO do servidor:               Linux
-- HeidiSQL Versão:              12.17.0.7270
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- A despejar dados para tabela db1241094.agents: ~9 rows (aproximadamente)
INSERT INTO `agents` (`id`, `nome`, `email`, `password`, `perfil`, `last_login`, `created_at`, `updated_at`, `agent_ativo`, `id_perfil`) VALUES
	(1, 'Carla Mendes', _binary 0xfa51ff868bb2f34e9d75df1ad4aff0d01e93ef52007a700b83800987997f90ec, '$2y$10$XWhyWtFRCY3Sia9TK/uEjeix1iqi6zwdF9biNvjlxBdFmIqTIfVbu', 'Administrador', '2026-06-24 02:59:05', '2026-06-21 11:01:58', '2026-06-24 02:59:05', 1, 1),
	(2, 'Ricardo Silva', _binary 0x2c34d1c9f9a3a0c29038cc5d8ed8a4e1afa171b48005792b13930bd0203b337e, '$2y$10$fnrNsAaBrqSox0ZesvsQe.Ufqx07BFTyZvBL8HM/tzYr6cJU3HgCG', 'Administrador', NULL, '2026-06-21 11:01:58', '2026-06-23 10:47:48', 1, 1),
	(3, 'Beatriz Costa', _binary 0x04c49d46c625670da35fc88142ff63e0afa171b48005792b13930bd0203b337e, '$2y$10$fnrNsAaBrqSox0ZesvsQe.Ufqx07BFTyZvBL8HM/tzYr6cJU3HgCG', 'Administrador', NULL, '2026-06-21 11:01:58', '2026-06-23 10:47:48', 1, 1),
	(4, 'Hugo Pereira', _binary 0x09d7b195511080b65aff236aafe88309bf31e657d7fcf67c7d90c6651006b406, '$2y$10$wwgSzGV43.ab2VUFv.UBz.syQT7hPzU1w4BSRJZZkMFyTJ3nE6zPm', 'Técnico', '2026-06-24 02:31:12', '2026-06-21 11:02:00', '2026-06-24 02:31:12', 1, 2),
	(5, 'Joana Ribeiro', _binary 0x4021606ab66847016b51718b3e532b1bbf31e657d7fcf67c7d90c6651006b406, '$2y$10$wwgSzGV43.ab2VUFv.UBz.syQT7hPzU1w4BSRJZZkMFyTJ3nE6zPm', 'Técnico', NULL, '2026-06-21 11:02:00', '2026-06-23 10:47:48', 1, 2),
	(6, 'André Fernandes', _binary 0xc0c5c082818aaf6962c4fd3cb528996cbf31e657d7fcf67c7d90c6651006b406, '$2y$10$wwgSzGV43.ab2VUFv.UBz.syQT7hPzU1w4BSRJZZkMFyTJ3nE6zPm', 'Técnico', NULL, '2026-06-21 11:02:00', '2026-06-23 10:47:48', 1, 2),
	(7, 'Filipe Gonçalves', _binary 0x55d71edb99a85e38b3f75491743877bcafa171b48005792b13930bd0203b337e, '$2y$10$fnrNsAaBrqSox0ZesvsQe.Ufqx07BFTyZvBL8HM/tzYr6cJU3HgCG', 'Profissional de Saude', '2026-06-24 02:39:04', '2026-06-21 11:02:00', '2026-06-24 02:39:04', 1, 3),
	(8, 'Marta Oliveira', _binary 0xa78875cb483c49bc4a9e43d99af9f0b3afa171b48005792b13930bd0203b337e, '$2y$10$fnrNsAaBrqSox0ZesvsQe.Ufqx07BFTyZvBL8HM/tzYr6cJU3HgCG', 'Profissional de Saude', NULL, '2026-06-21 11:02:00', '2026-06-23 10:53:42', 1, 3),
	(9, 'Tiago Santos', _binary 0x693f80b0b6bf96591bf722b3ebc3bc3aafa171b48005792b13930bd0203b337e, '$2y$10$fnrNsAaBrqSox0ZesvsQe.Ufqx07BFTyZvBL8HM/tzYr6cJU3HgCG', 'Profissional de Saude', NULL, '2026-06-21 11:02:00', '2026-06-23 10:53:42', 1, 3);

-- A despejar dados para tabela db1241094.categorias_equipamento: ~7 rows (aproximadamente)
INSERT INTO `categorias_equipamento` (`id`, `nome`) VALUES
	(1, 'Monitorização'),
	(2, 'Suporte de vida'),
	(3, 'Terapia'),
	(4, 'Diagnóstico'),
	(5, 'Laboratório'),
	(6, 'Esterilização'),
	(7, 'Reabilitação');

-- A despejar dados para tabela db1241094.consumivel: ~0 rows (aproximadamente)

-- A despejar dados para tabela db1241094.criticidades: ~4 rows (aproximadamente)
INSERT INTO `criticidades` (`id`, `nome`) VALUES
	(1, 'Baixa'),
	(2, 'Média'),
	(3, 'Alta'),
	(4, 'Suporte de vida');

-- A despejar dados para tabela db1241094.documento: ~1 rows (aproximadamente)
INSERT INTO `documento` (`id`, `id_equipamento`, `id_fornecedor`, `tipo`, `nome`, `data_documento`, `data_validade`, `documento_ativo`, `ficheiro`, `ficheiro_nome_original`, `id_tipo_documento`) VALUES
	(1, 1, NULL, 'Manual', 'Manual Utilizador Monitor Cardíaco', '2022-01-10', NULL, 1, 'doc_6a3668228b91e.pdf', 'Ficha 13 Update GET v02.pdf', NULL),
	(9, 5, NULL, 'Fatura', 'Fatura De Aquisição', '2023-03-13', '2029-11-22', 1, 'doc_6a37f70735963.pdf', 'faturaecografo.pdf', 5),
	(10, 7, NULL, 'Relatório Técnico', 'relatorio_inspecao_autoclave.pdf', '2026-06-22', NULL, 1, 'doc_6a395a2032f00.pdf', 'relatorio_inspecao_autoclave.pdf', 7),
	(11, 5, NULL, 'Fatura', 'Manual Ecógrafo', '2010-08-18', '2027-08-18', 1, 'doc_6a395a6973784.pdf', 'manual_servico_ecografo.pdf', 5),
	(12, 6, NULL, 'Certificado', 'Certificado Do Monitor Multiparamétrico', '2016-07-15', '2026-07-15', 1, 'doc_6a395ad307f46.pdf', 'certificado_calibracao_2024.pdf', NULL),
	(13, 2, NULL, 'Relatório Técnico', 'Relatório Do Ventilador Pulmonar', '2020-02-10', '2026-08-10', 1, 'doc_6a395b2deb4ad.pdf', 'relatorio_manutencao_drager.pdf', 7),
	(14, 3, NULL, 'Declaração', 'Conformidade Do Desfribilhador', '2024-04-16', '2040-04-16', 1, 'doc_6a395b75db4c3.pdf', 'declaracao_conformidade_zoll.pdf', NULL),
	(15, 6, NULL, 'Manual', 'Monitor Multiparamétrico', '2022-06-24', '2026-06-25', 1, 'doc_6a395bcd84b4a.pdf', 'manual_monitor_philips.pdf', NULL),
	(16, 11, NULL, 'Relatório Técnico', 'relatorio_manutencao_drager.pdf', '2026-06-23', NULL, 1, 'doc_6a3a8b50143ff.pdf', 'relatorio_manutencao_drager.pdf', 7);

-- A despejar dados para tabela db1241094.equipamento: ~26 rows (aproximadamente)
INSERT INTO `equipamento` (`id`, `codigo_interno`, `nome`, `categoria`, `marca`, `modelo`, `num_serie`, `fabricante`, `data_aquisicao`, `ano_fabrico`, `custo`, `tipo_entrada`, `estado`, `equipamento_ativo`, `criticidade`, `observacoes`, `id_localizacao`, `id_categoria`, `id_estado`, `id_criticidade`, `id_tipo_entrada`) VALUES
	(1, 'EQ001', 'Monitor Cardíaco', 'Monitorização', 'Philips', 'MX450', 'SN001', 'Philips', '2022-01-10', 2021, 5000.00, 'Compra', 'Ativo', 1, 'Alta', NULL, 1, 1, 1, 3, 1),
	(2, 'EQ002', 'Ventilador Pulmonar', 'Suporte de vida', 'Dräger', 'Evita V500', 'SN002', 'Dräger', '2021-06-15', 2020, 12000.00, 'Compra', 'Ativo', 1, 'Suporte de vida', '', 2, 2, 1, 4, 1),
	(3, 'EQ003', 'Desfibrilhador', 'Suporte de vida', 'Zoll', 'R Series', 'SN003', 'Zoll', '2023-03-20', 2022, 8000.00, 'Compra', 'Em manutenção', 1, 'Alta', NULL, 3, 2, 2, 3, 1),
	(4, 'EQ004', 'Bomba de Infusão', 'Terapia', 'B. Braun', 'Infusomat Space', 'SN004', 'B. Braun', '2021-09-05', 2020, 3000.00, 'Compra', 'Ativo', 0, 'Média', '', 4, 3, 1, 2, 1),
	(5, 'EQ005', 'Ecógrafo', 'Diagnóstico', 'Siemens', 'ACUSON X300', 'SN005', 'Siemens', '2020-11-12', 2019, 25000.00, 'Compra', 'Ativo', 1, 'Alta', '', 6, 4, 1, 3, 1),
	(6, 'EQ006', 'Monitor Multiparamétrico', 'Monitorização', 'Philips', 'IntelliVue MP5', 'SN006', 'Philips', '2022-04-18', 2021, 6000.00, 'Compra', 'Ativo', 1, 'Alta', NULL, 6, 1, 1, 3, 1),
	(7, 'EQ007', 'Autoclave', 'Esterilização', 'Getinge', 'GSS67H', 'SN007', 'Getinge', '2019-07-22', 2018, 15000.00, 'Compra', 'Ativo', 1, 'Média', '', 2, 6, 1, 2, 1),
	(8, 'EQ008', 'Oxímetro de Pulso', 'Monitorização', 'Nonin', 'Model 9590', 'SN008', 'Nonin', '2023-01-30', 2022, 800.00, 'Compra', 'Ativo', 1, 'Média', NULL, 1, 1, 1, 2, 1),
	(9, 'EQ009', 'Cama Articulada', 'Reabilitação', 'Stryker', 'InTouch', 'SN009', 'Stryker', '2021-03-14', 2020, 4500.00, 'Compra', 'Ativo', 1, 'Baixa', NULL, 4, 7, 1, 1, 1),
	(10, 'EQ010', 'Eletrocardiógrafo', 'Diagnóstico', 'GE Healthcare', 'MAC 5500', 'SN010', 'GE Healthcare', '2020-08-25', 2019, 7000.00, 'Compra', 'Inativo', 1, 'Média', '', 9, 4, 6, 2, 1),
	(11, 'EQ011', 'Analisador Bioquímico', 'Laboratório', 'Roche', 'Cobas c 311', 'SN011-B', 'Roche', '2022-06-10', 2020, 1800.00, 'Compra', 'Ativo', 1, 'Média', '', 6, 5, 1, 2, 1),
	(12, 'EQ012', 'Ventilador UCI', 'Suporte de vida', 'Dräger', 'Savina 300', 'SN012', 'Dräger', '2021-12-05', 2020, 11000.00, 'Compra', 'Ativo', 1, 'Suporte de vida', '', 2, 2, 1, 4, 1),
	(13, 'EQ013', 'Bomba Seringa', 'Terapia', 'B. Braun', 'Perfusor Space', 'SN013', 'B. Braun', '2023-02-15', 2022, 2500.00, 'Compra', 'Ativo', 1, 'Média', NULL, 3, 3, 1, 2, 1),
	(14, 'EQ014', 'Monitor de Pressão', 'Monitorização', 'Philips', 'SureSigns VS4', 'SN014', 'Philips', '2022-09-20', 2021, 3500.00, 'Compra', 'Em manutenção', 1, 'Alta', '', 1, 1, 2, 3, 1),
	(15, 'EQ015', 'Desfibrilhador DEA', 'Suporte de vida', 'Philips', 'HeartStart FRx', 'SN015', 'Philips', '2020-05-08', 2019, 1800.00, 'Compra', 'Ativo', 1, 'Suporte de vida', NULL, 2, 2, 1, 4, 1),
	(16, 'EQ016', 'Microscópio Clínico', 'Laboratório', 'Olympus', 'CX23', 'SN016', 'Olympus', '2019-11-30', 2018, 5500.00, 'Compra', 'Ativo', 1, 'Baixa', NULL, 6, 5, 1, 1, 1),
	(17, 'EQ017', 'Centrifugadora', 'Laboratório', 'Eppendorf', '5810R', 'SN017', 'Eppendorf', '2021-04-22', 2020, 4000.00, 'Compra', 'Ativo', 1, 'Baixa', NULL, 2, 5, 1, 1, 1),
	(18, 'EQ018', 'Cadeira de Rodas Elétrica', 'Reabilitação', 'Permobil', 'M3 Corpus', 'SN018', 'Permobil', '2022-07-14', 2021, 8500.00, 'Compra', 'Ativo', 1, 'Baixa', '', 4, 7, 1, 1, 1),
	(19, 'EQ019', 'Laringoscópio', 'Diagnóstico', 'Welch Allyn', 'MacroView', 'SN019', 'Welch Allyn', '2023-03-01', 2022, 1200.00, 'Compra', 'Ativo', 1, 'Alta', NULL, 3, 4, 1, 3, 1),
	(20, 'EQ020', 'Incubadora Neonatal', 'Suporte de vida', 'Dräger', 'Caleo', 'SN020', 'Dräger', '2020-09-18', 2019, 22000.00, 'Compra', 'Ativo', 1, 'Suporte de vida', '', 8, 2, 1, 4, 1),
	(21, 'EQ021', 'Tensiómetro Digital', 'Monitorização', 'Omron', 'M6 Comfort', 'SN021', 'Omron', '2023-01-05', 2022, 150.00, 'Compra', 'Ativo', 1, 'Baixa', '', 2, 1, 1, 1, 1),
	(22, 'EQ022', 'Estetoscópio Eletrónico', 'Diagnóstico', '3M Littmann', 'CORE', 'SN022', '3M', '2022-11-20', 2021, 400.00, 'Compra', 'Ativo', 1, 'Baixa', NULL, 6, 4, 1, 1, 1),
	(23, 'EQ023', 'Carro de Emergência', 'Suporte de vida', 'Harloff', 'C8T', 'SN023', 'Harloff', '2021-08-10', 2020, 3200.00, 'Compra', 'Ativo', 1, 'Alta', NULL, 1, 2, 1, 3, 1),
	(24, 'EQ024', 'Oftalmoscópio', 'Diagnóstico', 'Welch Allyn', 'PanOptic', 'SN024', 'Welch Allyn', '2022-05-25', 2021, 900.00, 'Compra', 'Abatido', 1, 'Baixa', NULL, 5, 4, 5, 1, 1),
	(25, 'EQ025', 'Nebulizador', 'Terapia', 'PARI', 'TurboBOY N', 'SN025', 'PARI', '2023-04-10', 2022, 280.00, 'Compra', 'Ativo', 1, 'Média', NULL, 8, 3, 1, 2, 1),
	(28, 'EQ030', 'Analisador Bioquímico', 'Suporte de vida', 'Roche', 'Cobas c 311', 'SN011', 'Roche', '2020-06-17', 2010, 20000.00, 'Compra', 'Ativo', 1, 'Média', '', 10, 2, 1, 2, 1);

-- A despejar dados para tabela db1241094.equipamento_fornecedor: ~25 rows (aproximadamente)
INSERT INTO `equipamento_fornecedor` (`id_equipamento`, `id_fornecedor`) VALUES
	(1, 1),
	(2, 1),
	(2, 8),
	(4, 4),
	(4, 5),
	(5, 1),
	(6, 1),
	(7, 5),
	(8, 4),
	(9, 4),
	(10, 1),
	(11, 1),
	(12, 4),
	(12, 5),
	(13, 5),
	(14, 1),
	(14, 4),
	(15, 1),
	(16, 4),
	(17, 4),
	(18, 4),
	(18, 5),
	(19, 4),
	(20, 5),
	(20, 10),
	(21, 4),
	(21, 5),
	(22, 4),
	(23, 5),
	(24, 4),
	(25, 4),
	(28, 2),
	(28, 4);

-- A despejar dados para tabela db1241094.estados_equipamento: ~6 rows (aproximadamente)
INSERT INTO `estados_equipamento` (`id`, `nome`) VALUES
	(1, 'Ativo'),
	(2, 'Em manutenção'),
	(3, 'Em calibração'),
	(4, 'Em quarentena'),
	(5, 'Abatido'),
	(6, 'Inativo');

-- A despejar dados para tabela db1241094.fornecedor: ~9 rows (aproximadamente)
INSERT INTO `fornecedor` (`id`, `nome`, `nif`, `telefone`, `email`, `morada`, `website`, `pessoa_contacto`, `telefone_contacto`, `tipo`, `fornecedor_ativo`, `observacoes`, `id_tipo_fornecedor`) VALUES
	(1, 'Philips Portugal', '500123001', '210000001', 'philips@exemplo.pt', 'Rua da Saúde 1, Lisboa', 'www.philips.pt', 'Ana Silva', '912000001', 'Fabricante', 1, NULL, 1),
	(2, 'Dräger Portugal', '500123002', '210000002', 'drager@exemplo.pt', 'Rua da Saúde 2, Lisboa', 'www.drager.pt', 'João Costa', '912000002', 'Fabricante', 1, NULL, 1),
	(4, 'MedTech Lda', '500123004', '210000004', 'medtech@exemplo.pt', 'Rua da Saúde 4, Porto', 'www.medtech.pt', 'Pedro Ferreira', '912000004', 'Distribuidor / Fornecedor Comercial', 1, NULL, 2),
	(5, 'SaúdeTec', '500123005', '210000005', 'saudetec@exemplo.pt', 'Rua da Saúde 5, Braga', 'www.saudetec.pt', 'Carla Oliveira', '912000005', 'Assistência Técnica', 1, NULL, 3),
	(6, 'B. Braun Portugal', '500123006', '210000006', 'bbraun@exemplo.pt', 'Rua da Saúde 6, Lisboa', 'www.bbraun.pt', 'Rui Martins', '912000006', 'Fabricante', 0, '', 1),
	(7, 'Siemens Healthineers', '500123007', '210000007', 'siemens@exemplo.pt', 'Rua da Saúde 7, Lisboa', 'www.siemens.pt', 'Sofia Pereira', '912000007', 'Fabricante', 0, NULL, 1),
	(8, 'ManuTec', '500123008', '210000008', 'manutec@exemplo.pt', 'Rua da Saúde 8, Coimbra', 'www.manutec.pt', 'Bruno Rodrigues', '912000008', 'Assistência Técnica', 1, NULL, 3),
	(9, 'MedSupply', '500123009', '210000009', 'medsupply@exemplo.pt', 'Rua da Saúde 9, Porto', 'www.medsupply.pt', 'Teresa Alves', '912000009', 'Fornecedor de Consumíveis', 1, NULL, 4),
	(10, 'TechMed', '500123010', '210000010', 'techmed@exemplo.pt', 'Rua da Saúde 10, Faro', 'www.techmed.pt', 'Nuno Sousa', '912000010', 'Distribuidor / Fornecedor Comercial', 1, NULL, 2);

-- A despejar dados para tabela db1241094.garantia_contrato: ~10 rows (aproximadamente)
INSERT INTO `garantia_contrato` (`id`, `id_equipamento`, `data_inicio`, `data_fim`, `tem_contrato`, `tipo_contrato`, `entidade_responsavel`, `periodicidade`, `observacoes`, `garantia_ativo`, `id_tipo_contrato`, `id_periodicidade`) VALUES
	(1, 1, '2022-01-10', '2025-01-10', 0, 'Garantia', 'Philips Portugal', 'Anual', NULL, 1, 1, 4),
	(2, 2, '2021-06-15', '2024-06-15', 0, 'Contrato de Manutenção', 'Dräger Portugal', 'Semestral', NULL, 1, 2, 3),
	(3, 3, '2023-03-20', '2026-03-20', 0, 'Garantia', 'Zoll Medical', 'Anual', NULL, 1, 1, 4),
	(4, 5, '2020-11-12', '2023-11-12', 0, 'Contrato de Manutenção', 'Siemens Healthineers', 'Trimestral', NULL, 1, 2, 2),
	(5, 6, '2022-04-18', '2025-04-18', 0, 'Garantia', 'Philips Portugal', 'Anual', NULL, 1, 1, 4),
	(6, 11, '2022-06-10', '2025-06-10', 0, 'Contrato de Manutenção', 'Siemens Healthineers', 'Semestral', '', 1, 2, 3),
	(7, 12, '2021-12-05', '2024-12-05', 0, 'Garantia', 'Dräger Portugal', 'Anual', NULL, 1, 1, 4),
	(8, 15, '2020-05-08', '2023-05-08', 0, 'Garantia', 'Philips Portugal', 'Anual', NULL, 1, 1, 4),
	(9, 20, '2020-09-18', '2023-09-18', 0, 'Contrato de Manutenção', 'Dräger Portugal', 'Trimestral', NULL, 1, 2, 2),
	(10, 28, '2020-06-18', '2026-06-30', 0, 'Garantia', 'Roche', 'Trimestral', '', 1, 1, 2);

-- A despejar dados para tabela db1241094.localizacao: ~10 rows (aproximadamente)
INSERT INTO `localizacao` (`id`, `edificio`, `piso`, `servico`, `sala`, `localizacao_ativo`) VALUES
	(1, 'Edifício Principal', 'Piso 0', 'Urgência', 'Sala 1', 1),
	(2, 'Edifício Principal', 'Piso 1', 'UCI', 'Sala 2', 1),
	(3, 'Edifício Principal', 'Piso 2', 'Bloco Operatório', 'Sala 3', 1),
	(4, 'Edifício Principal', 'Piso 1', 'Internamento', 'Sala 4', 1),
	(5, 'Edifício B', 'Piso 0', 'Consultas Externas', 'Sala 5', 0),
	(6, 'Edifício B', 'Piso 1', 'Cardiologia', 'Sala 6', 1),
	(7, 'Edifício C', 'Piso 0', 'Armazém', 'Armazém 1', 0),
	(8, 'Edifício Principal', 'Piso 3', 'Pediatria', 'Sala 7', 1),
	(9, 'Edifício B', '2', 'Internamento', 'Sala 4', 1),
	(10, 'Edifício B', '2', 'Internamento', 'Sala 4', 1);

-- A despejar dados para tabela db1241094.logs: ~2 rows (aproximadamente)
INSERT INTO `logs` (`id`, `tipo_evento`, `descricao`, `agente_id`, `ip`, `created_at`) VALUES
	(1, 'LOGIN_OK', 'Login efetuado com sucesso pelo agente: Carla Mendes', 1, '127.0.0.1', '2026-06-22 10:24:34'),
	(2, 'LOGIN_OK', 'Login efetuado com sucesso pelo agente: Carla Mendes', 1, '127.0.0.1', '2026-06-22 10:33:40'),
	(3, 'DADOS_ALTERADOS', 'Equipamento editado (id: 7): Autoclave', NULL, '127.0.0.1', '2026-06-22 15:44:47'),
	(4, 'DADOS_ALTERADOS', 'Equipamento editado (id: 4): Bomba de Infusão', NULL, '127.0.0.1', '2026-06-22 15:45:20'),
	(5, 'DADOS_ALTERADOS', 'Equipamento editado (id: 4): Bomba de Infusão', NULL, '127.0.0.1', '2026-06-22 15:45:23'),
	(6, 'DADOS_ALTERADOS', 'Equipamento editado (id: 7): Autoclave', NULL, '127.0.0.1', '2026-06-22 15:52:00'),
	(7, 'DADOS_ALTERADOS', 'Documento inserido: Manual Ecógrafo (tipo: Fatura)', NULL, '127.0.0.1', '2026-06-22 15:53:14'),
	(8, 'DADOS_ALTERADOS', 'Documento inserido: Certificado Do Monitor Multiparamétrico (tipo: Certificado)', NULL, '127.0.0.1', '2026-06-22 15:54:59'),
	(9, 'DADOS_ALTERADOS', 'Documento inserido: Relatório Do Ventilador Pulmonar (tipo: Relatório Técnico)', NULL, '127.0.0.1', '2026-06-22 15:56:31'),
	(10, 'DADOS_ALTERADOS', 'Documento inserido: Conformidade Do Desfribilhador (tipo: Declaração)', NULL, '127.0.0.1', '2026-06-22 15:57:42'),
	(11, 'DADOS_ALTERADOS', 'Documento inserido: Monitor Multiparamétrico (tipo: Manual)', NULL, '127.0.0.1', '2026-06-22 15:59:10'),
	(12, 'LOGIN_FALHOU', 'Tentativa de login falhada para o email: admin@medstock.pt', NULL, '127.0.0.1', '2026-06-23 10:20:42'),
	(13, 'LOGIN_FALHOU', 'Tentativa de login falhada para o email: admin@medstock.pt', NULL, '127.0.0.1', '2026-06-23 10:20:53'),
	(14, 'LOGIN_OK', 'Login efetuado com sucesso pelo agente: Carla Mendes', 1, '127.0.0.1', '2026-06-23 10:30:03'),
	(15, 'LOGIN_FALHOU', 'Tentativa de login falhada para o email: tecnico@medstock.pt', NULL, '127.0.0.1', '2026-06-23 10:44:23'),
	(16, 'LOGIN_FALHOU', 'Tentativa de login falhada para o email: tecnico1@medstock.pt', NULL, '127.0.0.1', '2026-06-23 10:44:36'),
	(17, 'LOGIN_FALHOU', 'Tentativa de login falhada para o email: tecnico@medstock.pt', NULL, '127.0.0.1', '2026-06-23 10:44:49'),
	(18, 'LOGIN_FALHOU', 'Tentativa de login falhada para o email: saude@medstock.pt', NULL, '127.0.0.1', '2026-06-23 10:45:35'),
	(19, 'LOGIN_FALHOU', 'Tentativa de login falhada para o email: tecnico@medstock.pt', NULL, '127.0.0.1', '2026-06-23 10:48:22'),
	(20, 'LOGIN_FALHOU', 'Tentativa de login falhada para o email: tecnico@medstock.pt', NULL, '127.0.0.1', '2026-06-23 10:48:43'),
	(21, 'LOGIN_OK', 'Login efetuado com sucesso pelo agente: Hugo Pereira', 4, '127.0.0.1', '2026-06-23 10:49:31'),
	(22, 'LOGIN_FALHOU', 'Tentativa de login falhada para o email: saude1@medstock.pt', NULL, '127.0.0.1', '2026-06-23 10:49:45'),
	(23, 'LOGIN_FALHOU', 'Tentativa de login falhada para o email: saude1@medstock.pt', NULL, '127.0.0.1', '2026-06-23 10:49:53'),
	(24, 'LOGIN_FALHOU', 'Tentativa de login falhada para o email: saude1@medstock.pt', NULL, '127.0.0.1', '2026-06-23 10:50:02'),
	(25, 'LOGIN_FALHOU', 'Tentativa de login falhada para o email: saude1@medstock.pt', NULL, '127.0.0.1', '2026-06-23 10:50:39'),
	(26, 'LOGIN_FALHOU', 'Tentativa de login falhada para o email: saude@medstock.pt', NULL, '127.0.0.1', '2026-06-23 10:52:34'),
	(27, 'LOGIN_OK', 'Login efetuado com sucesso pelo agente: Filipe Gonçalves', 7, '127.0.0.1', '2026-06-23 10:54:03'),
	(28, 'LOGIN_OK', 'Login efetuado com sucesso pelo agente: Carla Mendes', 1, '127.0.0.1', '2026-06-23 10:54:32'),
	(29, 'LOGIN_OK', 'Login efetuado com sucesso pelo agente: Filipe Gonçalves', 7, '127.0.0.1', '2026-06-23 11:06:52'),
	(30, 'LOGIN_OK', 'Login efetuado com sucesso pelo agente: Carla Mendes', 1, '127.0.0.1', '2026-06-23 11:38:00'),
	(31, 'LOGIN_OK', 'Login efetuado com sucesso pelo agente: Filipe Gonçalves', 7, '127.0.0.1', '2026-06-23 13:26:09'),
	(32, 'LOGIN_OK', 'Login efetuado com sucesso pelo agente: Carla Mendes', 1, '127.0.0.1', '2026-06-23 13:33:34'),
	(33, 'DADOS_ALTERADOS', 'Equipamento editado (id: 11): Analisador Bioquímico', 1, '127.0.0.1', '2026-06-23 13:34:09'),
	(34, 'LOGIN_OK', 'Login efetuado com sucesso pelo agente: Carla Mendes', 1, '127.0.0.1', '2026-06-23 22:56:49'),
	(35, 'LOGIN_OK', 'Login efetuado com sucesso pelo agente: Carla Mendes', 1, '127.0.0.1', '2026-06-23 23:06:28'),
	(36, 'LOGIN_OK', 'Login efetuado com sucesso pelo agente: Carla Mendes', 1, '127.0.0.1', '2026-06-24 02:09:57'),
	(37, 'LOGIN_OK', 'Login efetuado com sucesso pelo agente: Hugo Pereira', 4, '127.0.0.1', '2026-06-24 02:28:27'),
	(38, 'LOGIN_FALHOU', 'Tentativa de login falhada para o email: saude1@medstock.ptsaude123', NULL, '127.0.0.1', '2026-06-24 02:30:17'),
	(39, 'LOGIN_OK', 'Login efetuado com sucesso pelo agente: Filipe Gonçalves', 7, '127.0.0.1', '2026-06-24 02:30:23'),
	(40, 'LOGIN_OK', 'Login efetuado com sucesso pelo agente: Hugo Pereira', 4, '127.0.0.1', '2026-06-24 02:31:12'),
	(41, 'LOGIN_OK', 'Login efetuado com sucesso pelo agente: Filipe Gonçalves', 7, '127.0.0.1', '2026-06-24 02:39:04'),
	(42, 'LOGIN_OK', 'Login efetuado com sucesso pelo agente: Carla Mendes', 1, '127.0.0.1', '2026-06-24 02:59:05');

-- A despejar dados para tabela db1241094.mensagem_contacto: ~2 rows (aproximadamente)
INSERT INTO `mensagem_contacto` (`id`, `nome`, `email`, `telemovel`, `mensagem`, `mensagem_lida`, `created_at`) VALUES
	(1, 'Gabriela Arantes', '1241094@isep.ipp.pt', '933837558', 'ola', 1, '2026-06-21 22:34:35'),
	(2, 'Francisco Gonçalves', 'francisco@gmail.com', '987654321', 'estou interessado!', 1, '2026-06-21 22:36:28');

-- A despejar dados para tabela db1241094.perfis: ~3 rows (aproximadamente)
INSERT INTO `perfis` (`id`, `nome`) VALUES
	(1, 'Administrador'),
	(2, 'Técnico'),
	(3, 'Profissional de Saude');

-- A despejar dados para tabela db1241094.periodicidades: ~4 rows (aproximadamente)
INSERT INTO `periodicidades` (`id`, `nome`) VALUES
	(1, 'Mensal'),
	(2, 'Trimestral'),
	(3, 'Semestral'),
	(4, 'Anual');

-- A despejar dados para tabela db1241094.tipos_contrato: ~3 rows (aproximadamente)
INSERT INTO `tipos_contrato` (`id`, `nome`) VALUES
	(1, 'Garantia'),
	(2, 'Contrato de Manutenção'),
	(3, 'Assistência Técnica');

-- A despejar dados para tabela db1241094.tipos_documento: ~7 rows (aproximadamente)
INSERT INTO `tipos_documento` (`id`, `nome`) VALUES
	(1, 'Manual de Utilizador'),
	(2, 'Manual de Serviço'),
	(3, 'Certificado de Calibração'),
	(4, 'Contrato de Manutenção'),
	(5, 'Fatura'),
	(6, 'Declaração de Conformidade'),
	(7, 'Relatório Técnico');

-- A despejar dados para tabela db1241094.tipos_entrada: ~4 rows (aproximadamente)
INSERT INTO `tipos_entrada` (`id`, `nome`) VALUES
	(1, 'Compra'),
	(2, 'Doação'),
	(3, 'Aluguer'),
	(4, 'Empréstimo');

-- A despejar dados para tabela db1241094.tipos_fornecedor: ~4 rows (aproximadamente)
INSERT INTO `tipos_fornecedor` (`id`, `nome`) VALUES
	(1, 'Fabricante'),
	(2, 'Distribuidor / Fornecedor Comercial'),
	(3, 'Assistência Técnica'),
	(4, 'Fornecedor de Consumíveis');

-- A despejar dados para tabela db1241094.utilizador: ~0 rows (aproximadamente)

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
