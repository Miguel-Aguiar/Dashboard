$(document).ready(() => {

	$('#dashboard').on('click', () => {
		window.location.reload()
	})

	$('#documentacao').on('click', () => {
		//load() - Faz o Request e substitui o conteudo - Usa o método get
			//$('#pagina').load('documentacao.html')

		//get(url, () => {})
			/*
			$.get('documentacao.html', data => {
				$('#pagina').html(data)
			})
			*/
		//post(url, () => {})
			$.post('documentacao.html', data => {
				$('#pagina').html(data)
			})
	})

	$('#suporte').on('click', () => {
		//$('#pagina').load('suporte.html')
		/*
		$.get('suporte.html', data => {
			$('#pagina').html(data)
		})
		*/
		$.post('suporte.html', data => {
			$('#pagina').html(data)
		})
	})

	//Ajax do app.php
	$('#competencia').on('change', e => {
		//Fazer a requisição e enviando dados
			//ajax() - Recebe um obj literal composto por:
			//método, url, dados, sucesso, erro
			//type, url, data, sucess, error 

			let competencia = $(e.target).val() 	

			$.ajax({
				type: 'GET',
				url: 'app.php',
				data: `competencia=${competencia}`,
				//O retorno da requisição é em html, podemos modificar o retorno com dataType
				dataType: 'json', 
				success: dados => {
					console.log(dados)
					$('#numeroVendas').html(dados.numeroVendas)
					$('#totalVendas').html(dados.totalVendas)
					$('#clientesAtivos').html(dados.clientesAtivos)
					$('#clientesInativos').html(dados.clientesInativos)
					$('#reclamacoes').html(dados.totalReclamacoes)
					$('#sugestoes').html(dados.totalSugestoes)
					$('#elogios').html(dados.totalElogios)
					$('#despesas').html(dados.totalDespesas)
				},
				error: erro => { console.log(erro)}
			})
	})

})
