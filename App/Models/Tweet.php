<?php
    namespace App\Models;

    use MF\Model\Model;

    class Tweet extends Model {

        private $id;
        private $id_usuario;
        private $tweet;
        private $data;

        public function __get($atributo){
            return $this->$atributo;
        }

        public function __set($atributo, $valor){
            $this->$atributo = $valor;
        }

        //salvar
        public function salvar(){
            $query_salvar = "insert into tb_tweets(id_usuario, tweet)values(:id_usuario, :tweet)";
            $stmt = $this->db->prepare($query_salvar);
            $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
            $stmt->bindValue(':tweet', $this->__get('tweet'));
            $stmt->execute();
            return $this;
        }

        //recuperar
        public function getAll() {
            $query_recuperar = "
                select 
                    t.id, t.id_usuario, u.nome, t.tweet, DATE_FORMAT(t.data, '%d/%m/%Y %H:%i') as data
                from 
                    tb_tweets as t
                    left join tb_usuarios as u on (t.id_usuario = u.id)
                where
                    t.id_usuario = :id_usuario
                    or t.id_usuario in (select id_usuario_seguindo from tb_usuarios_seguidores where id_usuario = :id_usuario)
                order by
                    t.data desc";
            $stmt = $this->db->prepare($query_recuperar);
            $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function removerTweet(){

            $query_remover_tweet = "delete from tb_tweets where id = :id";
            $stmt = $this->db->prepare($query_remover_tweet);
            $stmt->bindValue(':id',$this->__get('id'));
            $stmt->execute();

            return true;
        }
    }
?>