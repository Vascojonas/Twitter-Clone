<?php
	
	namespace App\Models;

	use MF\Model\Model;

	Class Usuario extends Model {
		private $id;
		private $nome;
		private $email;
		private $senha;

		public function __get($atributo){
			return $this->$atributo;
		}

		public function __set($atributo, $valor){
			$this->$atributo=$valor;
		}

		//salvar dados no banco
		public function salvar(){
			$query = "insert into usuarios(nome,email,senha) values(:nome, :email, :senha)";
			$stmt= $this->db->prepare($query);
			$stmt->bindValue(':nome', $this->__get('nome'));
			$stmt-> bindValue(':email', $this->__get('email'));
			$stmt->bindValue(':senha', $this->__get('senha'));
			$stmt-> execute();

			return $this;
		}

		//validar se um cadastro pode ser feito
		public function validarCadastro(){
			$controle=true;
			if(strlen($this->__get('nome'))<3){
				$controle=false;
			}
			if(strlen($this->__get('email'))<3){
				$controle=false;
			}
			if(strlen($this->__get('senha'))<3){
		
				$controle=false;
			}

			return $controle;
		}

		//recuperar por email
		public function getUsuarioPorEmail(){
			$query="select nome, email, senha from usuarios where email= :email";
			$stmt = $this->db->prepare($query);
			$stmt -> bindValue(':email', $this->__get('email'));
			$stmt ->execute();

			return $stmt->fetchAll(\PDO::FETCH_ASSOC);
		}

		public function autenticar(){
			$query="select id,nome , email from usuarios where email=:email and senha= :senha";
			$stmt = $this->db->prepare($query);
			$stmt-> bindValue(':email',$this->__get('email'));
			$stmt-> bindValue(':senha',$this->__get('senha'));
			$stmt->execute();

			$usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

			if($usuario){
				if($usuario['id']!='' && $usuario['nome']!=''){
				$this->__set('id', $usuario['id']);
				$this->__set('nome', $usuario['nome']);
				}
			}

			return $this; 

		}

		public function getAll(){
			$query="select 
					u.id, 
					u.nome, 
					u.email,
					( select count(*)
						from 
							usuarios_seguidores as us
						where
							id_usuario=:id and u.id=us.id_usuario_seguindo
					) as seguindo_sn 
				from 
					usuarios as u
				where 
					nome like :nome and id !=:id";
			$stmt= $this->db->prepare($query);
			$stmt->bindValue(':nome','%'.$this->__get('nome').'%');
			$stmt->bindValue(':id', $this->__get('id'));
			$stmt->execute();

			return $stmt->fetchAll(\PDO::FETCH_ASSOC);
		}

		public function seguir($id_usuario_seguindo){
			$query="insert into usuarios_seguidores (id_usuario, id_usuario_seguindo)
			values(:id_usuario, :id_usuario_seguindo)";
			$stmt= $this->db->prepare($query);
			$stmt->bindValue(':id_usuario',$this->__get('id'));
			$stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);
			$stmt->execute();
			return true;
		}

		public function deixarDeSeguir($id_usuario_seguindo){
			$query="delete 
				from 
					usuarios_seguidores 
			where 
				id_usuario=:id_usuario and id_usuario_seguindo=:id_usuario_seguindo";
			$stmt=$this->db->prepare($query);
			$stmt->bindValue(':id_usuario',$this->__get('id'));
			$stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);
			$stmt->execute();
			return true;
		}

		public function getInfoUsuario(){
			$query= "select nome from usuarios where id= :id_usuario";
			$stmt=$this->db->prepare($query);
			$stmt->bindValue(':id_usuario', $this->__get('id'));
			$stmt->execute();

			return $stmt->fetch(\PDO::FETCH_ASSOC);
		}

		public function getTotalTweets(){
			$query= "select count(*) as total_tweets from tweets where id_usuario= :id_usuario";
			$stmt=$this->db->prepare($query);
			$stmt->bindValue(':id_usuario', $this->__get('id'));
			$stmt->execute();

			return $stmt->fetch(\PDO::FETCH_ASSOC);
		}

		public function getTotalSeguindo(){
			$query= "select count(*) as seguindo from usuarios_seguidores where id_usuario= :id_usuario";
			$stmt=$this->db->prepare($query);
			$stmt->bindValue(':id_usuario', $this->__get('id'));
			$stmt->execute();

			return $stmt->fetch(\PDO::FETCH_ASSOC);
		}

		public function getTotalSeguidores(){
			$query= "select count(*) as seguidores from usuarios_seguidores where id_usuario_seguindo= :id_usuario";
			$stmt=$this->db->prepare($query);
			$stmt->bindValue(':id_usuario', $this->__get('id'));
			$stmt->execute();

			return $stmt->fetch(\PDO::FETCH_ASSOC);
		}
	}
?>