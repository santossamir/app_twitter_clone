<?php
    namespace App\Models;

    use MF\Model\Model;

    class Usuario extends Model {

        private $id;
        private $nome;
        private $email;
        private $senha;

        public function __get($atributo){
            return $this->$atributo;
        }

        public function __set($atributo, $valor){
            $this->$atributo = $valor;
        }

        //Salvar
        public function salvar(){
            
            $query = "insert into tb_usuarios(nome, email, senha)values(:nome, :email, :senha)";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':nome', $this->__get('nome'));
            $stmt->bindValue(':email', $this->__get('email'));
            $stmt->bindValue(':senha', $this->__get('senha'));
            $stmt->execute();

            return $this;
        }

        //Validar cadastro
        public function validarCadastro() {
            $valido = true;

            if(strlen($this->__get('nome')) < 3){
                $valido = false;
            }

            if(strlen($this->__get('email')) < 3){
                $valido = false;
            }

            if(strlen($this->__get('senha')) < 3){
                $valido = false;
            }

            return $valido;
        }
        //Recuperar usuario por e-mail
        public function getUsuarioPorEmail(){

            $query = "select nome, email from tb_usuarios where email = :email";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':email', $this->__get('email'));
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function validar(){

            $query_validar = "select id, nome, email from tb_usuarios where email = :email and senha = :senha";
            $stmt = $this->db->prepare($query_validar);
            $stmt->bindValue(':email', $this->__get('email'));
            $stmt->bindValue(':senha', $this->__get('senha'));
            $stmt->execute();

            $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if($usuario['id'] != '' && $usuario['nome'] != ''){
                $this->__set('id', $usuario['id']);
                $this->__set('nome', $usuario['nome']);
            }
            return $this;
        }

        public function getAll() {
            $query = "
            select
                u.id, u.nome, u.email, (
                    select
                        count(*)
                    from
                       tb_usuarios_seguidores as us
                    where
                        us.id_usuario = :id_usuario and us.id_usuario_seguindo = u.id
                ) as seguindo_sn
            from 
                tb_usuarios as u
            where
                u.nome like :nome and u.id != :id_usuario
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':nome', '%'.$this->__get('nome').'%');
            $stmt->bindValue(':id_usuario', $this->__get('id'));
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function seguirUsuario($id_usuario_seguindo) {
            $query_seguir = "insert into tb_usuarios_seguidores(id_usuario, id_usuario_seguindo)
                             value(:id_usuario, :id_usuario_seguindo)";
            $stmt = $this->db->prepare($query_seguir);
            $stmt->bindValue(':id_usuario' , $this->__get('id'));
            $stmt->bindValue(':id_usuario_seguindo' , $id_usuario_seguindo);
            $stmt->execute();

            return true;
        }

        public function deixarSeguirUsuario($id_usuario_seguindo) {
           $query_deixar_de_seguir = "delete from tb_usuarios_seguidores where id_usuario = :id_usuario and
                                      id_usuario_seguindo = :id_usuario_seguindo";
            $stmt = $this->db->prepare($query_deixar_de_seguir);
            $stmt->bindValue(':id_usuario', $this->__get('id'));
            $stmt->bindValue(':id_usuario_seguindo' , $id_usuario_seguindo);
            $stmt->execute();

            return true;
        }
    }
?>