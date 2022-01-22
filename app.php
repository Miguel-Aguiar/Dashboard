<?php
	// Criar as classes do objeto, estabelecer uma conexão com o banco de dados e recuperar seus registros 

	//Classe que contenha todos os atributos necessários para popular o dashboard
	Class Dashboard {

		public $data_inicio;
		public $data_fim;
		public $numeroVendas;
		public $totalVendas;
		public $clientesAtivos;
		public $clientesInativos;
		public $totalReclamacoes;
		public $totalSugestoes;
		public $totalElogios;
		public $totalDespesas;

		public function __get($attr) {
			return $this->$attr;
		}

		public function __set($attr, $value) {
			$this->$attr = $value;
			return $this;
		}
	}

	//Classe de conexao com o banco de dados
	Class Conexao {
		private $host = 'localhost';
		private $dbname = 'dashboard';
		private $user = 'root';
		private $pass = '';

		public function conectar() {
			try {
				$conexao = new PDO (
					"mysql:host=$this->host;dbname=$this->dbname",
					"$this->user",
					"$this->pass"
				);

				return $conexao;
			}
			catch(PDOException $e) {
				echo '<p>'.$e->getMessege().'</p>';
			}
		}
	}

	//Classe para trabalhar dentro do banco de dados
	Class Bd {
		private $conexao;
		private $dashboard;

		public function __construct(Conexao $conexao, Dashboard $dashboard) {

			$this->conexao = $conexao->conectar();
			$this->dashboard = $dashboard; 
		}

		public function getNumeroVendas() {
			$query = '
				select
					count(*) as numero_vendas
				from
					tb_vendas
				where
					data_venda between :data_inicio and :data_fim';
			
			$stmt = $this->conexao->prepare($query);
			$stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
			$stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
			$stmt->execute();

			return $stmt->fetch(PDO::FETCH_OBJ)->numero_vendas;
		}

		public function getTotalVendas() {
			$query = '
				select
					SUM(total) as total_vendas
				from
					tb_vendas
				where
					data_venda between :data_inicio and :data_fim';
			
			$stmt = $this->conexao->prepare($query);
			$stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
			$stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
			$stmt->execute();

			return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;
		}

		public function getClientesAtivos() {
			$query = '
				select
					COUNT(cliente_ativo) as ativos
				from
					tb_clientes
				where
				 cliente_ativo = 1';
					
			$stmt = $this->conexao->prepare($query);
			$stmt->execute();

			return $stmt->fetch(PDO::FETCH_OBJ)->ativos;	
		}

		public function getClientesInativos() {
			$query = '
				select
					COUNT(cliente_ativo) as inativos
				from
					tb_clientes
				where
				 cliente_ativo = 0';
					
			$stmt = $this->conexao->prepare($query);
			$stmt->execute();

			return $stmt->fetch(PDO::FETCH_OBJ)->inativos;	
		}

		public function getTotalReclamacoes() {
			$query = '
				select
					COUNT(tipo_contato) as reclamacoes
				from
					tb_contatos
				where
				 tipo_contato = 1';
					
			$stmt = $this->conexao->prepare($query);
			$stmt->execute();

			return $stmt->fetch(PDO::FETCH_OBJ)->reclamacoes;	
		}

		public function getTotalSugestoes() {
			$query = '
				select
					COUNT(tipo_contato) as sugestoes
				from
					tb_contatos
				where
				 tipo_contato = 2';
					
			$stmt = $this->conexao->prepare($query);
			$stmt->execute();

			return $stmt->fetch(PDO::FETCH_OBJ)->sugestoes;	
		}

		public function getTotalElogios() {
			$query = '
				select
					COUNT(tipo_contato) as elogios
				from
					tb_contatos
				where
				 tipo_contato = 3';
					
			$stmt = $this->conexao->prepare($query);
			$stmt->execute();

			return $stmt->fetch(PDO::FETCH_OBJ)->elogios;	
		}

		public function getDespesas() {
			$query = '
				select
					SUM(total) as despesas
				from
					tb_despesas';
					
			$stmt = $this->conexao->prepare($query);
			$stmt->execute();

			return $stmt->fetch(PDO::FETCH_OBJ)->despesas;	
		}

	}

	$dashboard = new Dashboard();

	$conexao = new Conexao();	

	//Recuperando dados do front-end
	$competencia = explode('-', $_GET['competencia']);
	$ano = $competencia[0];
	$mes =  $competencia[1];

	//Recuperar os dias do mês
	$dias_do_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

	$dashboard->__set('data_inicio', $ano. '-' . $mes . '-01');
	$dashboard->__set('data_fim', $ano. '-' . $mes . '-' . $dias_do_mes);

	$bd = new Bd($conexao, $dashboard);

	$dashboard->__set('numeroVendas', $bd->getNumeroVendas());
	$dashboard->__set('totalVendas', $bd->getTotalVendas());
	$dashboard->__set('clientesAtivos', $bd->getClientesAtivos());
	$dashboard->__set('clientesInativos', $bd->getClientesInativos());
	$dashboard->__set('totalReclamacoes', $bd->getTotalReclamacoes());
	$dashboard->__set('totalSugestoes', $bd->getTotalSugestoes());
	$dashboard->__set('totalElogios', $bd->getTotalElogios());
	$dashboard->__set('totalDespesas', $bd->getDespesas());

	//Enviar em formato json 
	echo json_encode($dashboard);
	
?>
