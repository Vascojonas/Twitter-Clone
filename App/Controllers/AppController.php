<?php

	namespace App\Controllers;

	//os recursos do miniframework
	use MF\Controller\Action;
	use MF\Model\Container;

	class AppController extends Action {

		public function timeline(){

			$this->validarAutenticacao();

				$tweet= Container::getModel('tweets');
				$tweet->__set('id_usuario', $_SESSION['id']);
				$tweets = $tweet->getAll();
				
				$this->view->tweet=$tweets;

				$usuario= Container::getModel('Usuario');
				$usuario->__set('id',$_SESSION['id']);

				$this->view->infoUsuario=$usuario->getInfoUsuario();
				$this->view->totalTweets=$usuario->getTotalTweets();
				$this->view->totalSeguindo=$usuario->getTotalSeguindo();
				$this->view->totalSeguidores=$usuario->getTotalSeguidores();
				
				$this->render('timeline');			
		}

		public function tweet(){

			$this->validarAutenticacao();
				$tweet = Container::getModel('tweets');
				$tweet->__set('id_usuario', $_SESSION['id'] );
				$tweet->__set('tweet', $_POST['tweet'] );

				$tweet->salvar();
				$this->timeline();
		}

		public function validarAutenticacao(){
			session_start();
			if(!isset($_SESSION['id'])||$_SESSION['id']==''|| !isset($_SESSION['nome'])||$_SESSION['nome']==''){
				header('Location: /login=erro');
			}
		}


		public function quemSeguir(){
			$this->validarAutenticacao();
			
			$pesquisarPor= isset($_GET['pesquisarPor']) ?$_GET['pesquisarPor']: "";

			$usuarios= array();

			if ($pesquisarPor !="") {
			 	$usuario = Container::getModel('Usuario');
			 	$usuario->__set('nome', $pesquisarPor);
			 	$usuario->__set('id', $_SESSION['id']);
			 	$usuarios = $usuario->getAll();
			 }

			 $this->view->usuario=$usuarios;

			 $usuario= Container::getModel('Usuario');
				$usuario->__set('id',$_SESSION['id']);

				$this->view->infoUsuario=$usuario->getInfoUsuario();
				$this->view->totalTweets=$usuario->getTotalTweets();
				$this->view->totalSeguindo=$usuario->getTotalSeguindo();
				$this->view->totalSeguidores=$usuario->getTotalSeguidores();

			$this->render('quemSeguir');
		}

		public function acao(){
			$this->validarAutenticacao();

			$acao = isset($_GET['acao'])?$_GET['acao']: '';
			$id_usuario_seguindo=isset($_GET['id_usuario'])?$_GET['id_usuario']:'';
			$usuario = Container::getModel('Usuario');
			$usuario->__set('id', $_SESSION['id']);

			if($acao=='seguir'){
				$usuario->seguir($id_usuario_seguindo);
				header('Location:/quem_seguir');
			}else if($acao=='deixar_de_seguir'){
				$usuario->deixarDeSeguir($id_usuario_seguindo);
				header('Location:/quem_seguir');
			}

			if ($acao=='remover_tweet') {
				$tweet=Container::getModel('Tweets');
				$tweet->__set('id', $_GET['id_tweet']);
				$tweet->remove();

				header('Location: /timeline');
			}


		}
	}

?>