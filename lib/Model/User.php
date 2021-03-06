<?php
namespace MyApp\Model;

class User Extends \MyApp\Model {
  public function create($values){
    global $AES_KEY;
    $stmt = $this->db->prepare("insert into m_user (id,name,duRank,depId,area,block,post,pass) values (:id,:name,:duRank,:depId,:area,:block,:post,HEX(AES_ENCRYPT(:password,'{$AES_KEY}')))");
    $stmt->bindValue(':id',$values['id']);
    $stmt->bindValue(':name',$values['name']);
    $stmt->bindValue(':duRank',$values['duRank']);
    $stmt->bindValue(':depId',$values['depId']);
    $stmt->bindValue(':area',$values['area']);
    $stmt->bindValue(':block',$values['block']);
    $stmt->bindValue(':post',$values['post']);
    $stmt->bindValue(':password',$values['password']);
    $stmt->execute();

    if($res === false){
      throw new \MyApp\Exception\DuplicateId();
    }
  }

  public function login($values){
    global $AES_KEY;
    $stmt = $this->db->prepare("select `id`,`name`,`duRank`,`depId`,`area`,`block`,`post`,CONVERT(AES_DECRYPT(UNHEX(`pass`),'{$AES_KEY}') using utf8) as password from m_user where id = :id");
    $stmt->bindValue(':id',$values['id']);
    $stmt->execute();
    $stmt->setFetchMode(\PDO::FETCH_CLASS,'stdClass');
    $user = $stmt->fetch();

    if(empty($user)){
      throw new \MyApp\Exception\UnmatchId();
    }
    if($values['password'] !== $user->password){
      throw new \MyApp\Exception\UnmatchPassword();
    }
    return $user;
  }

  public function findAll(){
    global $AES_KEY;
    $stmt = $this->db->query("select * from m_user order by id");
    $stmt->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');
    return $stmt->fetchAll();
  }
}
?>
