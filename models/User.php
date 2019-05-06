<?php
class User{
  //db stuff
  private $conn;
  private $table = 'users';

  //user properties
  public $user_id;
  public $user_firstname;
  public $user_lastname;
  public $user_image;
  public $user_email;
  public $user_password;
  public $user_role;
  public $time_online;
  public $code_one;
  public $code_two;
  public $active;

  //constructor with DB
  public function __construct($db){
    $this->conn = $db;
  }

  //function for checking if user email exist on database
  public function check_email_exist(){
    $query = "SELECT user_email FROM ". $this->table . " WHERE user_email = :email";
    //prepare statement
    $stmt = $this->conn->prepare($query);
    //bind category name
    $stmt->bindParam(':email', $this->user_email);
    //execute query
    $stmt->execute();
    //return stmt
    return $stmt;
  }
  //function for creating new user on database
  public function create_user(){
    $query = "INSERT INTO ". $this->table . "
      SET
        user_firstname = :user_firstname,
        user_lastname = :user_lastname,
        user_image = '',
        user_email = :user_email,
        user_password = :user_password,
        user_role = 'Subscriber',
        time_online = 0,
        code_one = :code_one,
        code_two = :code_two,
        active =  0";
        //prepare statement
        $stmt = $this->conn->prepare($query);
        //clean data
        $this->user_firstname = htmlentities(strip_tags($this->user_firstname));
        $this->user_lastname = htmlentities(strip_tags($this->user_lastname));
        $this->user_email = htmlentities(strip_tags($this->user_email));
        $this->user_password = htmlentities(strip_tags($this->user_password));
        //hashing password before saving it to data base
        $this->user_password = password_hash($this->user_password, PASSWORD_BCRYPT, array('cost'=>12));
        //setting code of user for updating or validating info
        $this->code_one = md5($this->user_email . microtime());
        $this->code_two = md5($this->user_password . microtime());
        //bind data
        $stmt->bindParam(':user_firstname', $this->user_firstname);
        $stmt->bindParam(':user_lastname', $this->user_lastname);
        $stmt->bindParam(':user_email', $this->user_email);
        $stmt->bindParam(':user_password', $this->user_password);
        $stmt->bindParam(':code_one', $this->code_one);
        $stmt->bindParam(':code_two', $this->code_two);
        //execute query
        if($stmt->execute()){
          return true;
        }
        //printing error for debugging
        printf("Error: %s.\n", $stmt->error);
        //return false if proplem occur during adding user on database
        return false;
  }
  //check if new user is added
  public function check_new_user_is_added(){
    $query = "SELECT user_firstname, user_lastname, user_email, code_one, code_two FROM ". $this->table . " WHERE user_email = :user_email AND active = 0 AND time_online = 0";
    //prepare statement
    $stmt = $this->conn->prepare($query);
    //bind data
    $stmt->bindParam(':user_email', $this->user_email);
    //execute query
    $stmt->execute();
    //return stmt
    return $stmt;
  }
  //function to check code one and two to activate user
  public function check_code_one_and_two_activate_user(){
    $query = "SELECT user_id FROM ". $this->table . " WHERE code_one = :code_one AND code_two = :code_two  AND active = 0 AND time_online = 0";
    //prepare statement
    $stmt = $this->conn->prepare($query);
    //bind code one and two
    $stmt->bindParam(':code_one', $this->code_one);
    $stmt->bindParam(':code_two', $this->code_two);
    //execute query
    $stmt->execute();
    //return stmt
    return $stmt;
  }
  //getting code one and two of user with registered email on database
  public function get_code_one_and_two(){
    $query = "SELECT code_one, code_two FROM ". $this->table . " WHERE user_email = :user_email AND active = 0 AND code_one != ''";
    //prepare statement
    $stmt = $this->conn->prepare($query);
    //bind email
    $stmt->bindParam(':user_email', $this->user_email);
    //execute query
    $stmt->execute();
    //return stmt
    return $stmt;
  }
  //activate user account on database
  public function activate_user_account(){
    $query ="UPDATE ". $this->table . " SET active = 1, code_two = 0 WHERE code_one = :code_one AND time_online = 0";
    //prepare statement
    $stmt = $this->conn->prepare($query);
    //bind code one
    $stmt->bindParam(':code_one', $this->code_one);
    //execute query
    if($stmt->execute()){
      //return true if it is updated succesfully
      return true;
    }
      //print error for debugging
      printf("Error: %s.\n", $stmt->error);
      return false;
  }
  //getting user password, user role and id of email that is registered on database
  public function check_user_login(){
    $query = "SELECT user_password, user_role, user_id FROM ". $this->table . " WHERE user_email = :user_email AND active = 1 AND code_one != ''";
    //prepare statement
    $stmt = $this->conn->prepare($query);
    //bind email
    $stmt->bindParam(':user_email', $this->user_email);
    //execute query
    $stmt->execute();
    //return stmt
    return $stmt;
  }
  // function to update time online of user to monitor how many user is online
  public function update_time_online(){
    //setting time
    $time = time();
    //setting query
    $query = "UPDATE ". $this->table . " SET time_online = ". $time . " WHERE user_id = :user_id AND code_one != '' ";
    //prepare statement
    $stmt = $this->conn->prepare($query);
    //bind user id
    $stmt->bindParam(':user_id', $this->user_id);
    //execute query
    if($stmt->execute()){
      //return true if it is updated succesfully
      return true;
    }
      //print error for debugging
      printf("Error: %s.\n", $stmt->error);
      return false;
  }
  //function to check if user is admin or subscriber
  public function get_user_role(){
    $query = "SELECT user_role FROM ". $this->table . " WHERE user_id = :id AND active = 1 AND time_online != 0";
    //prepare statement
    $stmt = $this->conn->prepare($query);
    //make sure id is absolute number
    $this->id = abs((int)$this->user_id);
    //bind user email
    $stmt->bindParam(':id', $this->id);
    //execute query
    $stmt->execute();
    //return stmt
    return $stmt;
  }
  //function to get user id and code to recover password of user wich is registered on database
  public function get_user_id_and_code_one_to_recover_password(){
    $query = "SELECT user_id, code_one FROM ". $this->table . " WHERE user_email = :user_email AND active = 1 AND code_one != ''";
    //prepare statement
    $stmt = $this->conn->prepare($query);
    //bind user email
    $stmt->bindParam(':user_email', $this->user_email);
    //execute query
    $stmt->execute();
    //return stmt
    return $stmt;
  }
  //function to update code two of user to recover password with its registerd email on database
  public function update_code_two_to_recover_password($code_two){
    $query = "UPDATE ". $this->table . " SET code_two = :code_two WHERE user_email = :user_email AND active = 1 AND code_one != ''";
    //prepare statement
    $stmt = $this->conn->prepare($query);
    //bind code two
    $stmt->bindParam(':code_two', $code_two);
    //bind email
    $stmt->bindParam(':user_email', $this->user_email);
    //execute query
    if($stmt->execute()){
      //return true if it is updated succesfully
      return true;
    }
      //print error for debugging
      printf("Error: %s.\n", $stmt->error);
      return false;
  }
  //function to get user id with valid code one and code two
  public function get_id_with_valid_recovery_password_code(){
    $query = "SELECT user_id FROM ". $this->table . " WHERE code_two = :code_two AND code_one = :code_one AND active = 1";
    //prepare statement
    $stmt = $this->conn->prepare($query);
    //bind code two
    $stmt->bindParam(':code_two', $this->reset_code);
    //bind code one
    $stmt->bindParam(':code_one', $this->code);
    //execute query
    $stmt->execute();
    //return stmt
    return $stmt;
  }
  //function to update forgot password on database
  public function change_forgot_password(){
    $query = "UPDATE ". $this->table . " SET user_password = :new_password, code_two = 0 WHERE user_id = :id  AND code_one != '' AND active = 1";
    //prepare statement
    $stmt = $this->conn->prepare($query);
    //make sure user id is absolute number
    $this->user_id = abs((int)$this->id);
    //hash new password before saving it on database
    $this->new_user_password = password_hash($this->new_password, PASSWORD_BCRYPT, array('cost'=>12));
    //bind data
    $stmt->bindParam(':new_password', $this->new_user_password);
    $stmt->bindParam(':id', $this->user_id);
    //execute query
    if($stmt->execute()){
      //return true if it is updated succesfully
      return true;
    }
      //print error for debugging
      printf("Error: %s.\n", $stmt->error);
      return false;
  }
  //function to change password on profile info
  public function update_password(){
    $query = "UPDATE ". $this->table . " SET user_password = :new_password, code_two = 0 WHERE user_id = :id AND active = 1 AND time_online != 0";
    //prepare statement
    $stmt = $this->conn->prepare($query);
    //make sure user id is absolute number
    $this->user_id = abs((int)$this->id);
    //hash new password before saving it on database
    $this->new_user_password = password_hash($this->new_password, PASSWORD_BCRYPT, array('cost'=>12));
    //bind data
    $stmt->bindParam(':new_password', $this->new_user_password);
    $stmt->bindParam(':id', $this->user_id);
    //execute query
    if($stmt->execute()){
      //return true if it is updated succesfully
      return true;
    }
      //print error for debugging
      printf("Error: %s.\n", $stmt->error);
      return false;
  }
  //check if password is successsfully updated
  public function check_password_is_updated(){
    $query = "SELECT user_password FROM ". $this->table . " WHERE user_id = :id AND code_two = 0 AND active = 1";
    //prepare statement
    $stmt = $this->conn->prepare($query);
    //make sure id is absolute number
    $this->user_id = abs((int)$this->id);
    //bind code two
    $stmt->bindParam(':id', $this->user_id);
    //execute query
    $stmt->execute();
    //return stmt
    return $stmt;
  }
  //function to get user info
  public function profile_info(){
    $query = "SELECT user_id, user_firstname, user_lastname, user_email, user_image FROM ". $this->table . " WHERE user_id = :id AND active = 1 AND time_online != 0";
    //prepare statement
    $stmt = $this->conn->prepare($query);
    //make sure id is absolute number
    $this->user_id = abs((int)$this->id);
    //bind data
    $stmt->bindParam(':id', $this->user_id);
    //execute query
    $stmt->execute();
    //return result
    return $stmt;
  }
  //check if user is online
  public function check_user_is_online(){
    $query = "SELECT user_id FROM ". $this->table . " WHERE user_id = :id AND active = 1 AND time_online != 0";
    //prepare statement
    $stmt = $this->conn->prepare($query);
    //make sure id is absolute number
    $this->user_id = abs((int)$this->id);
    //bind data
    $stmt->bindParam(':id', $this->user_id);
    //execute query
    $stmt->execute();
    //return result
    return $stmt;
  }
  //function to update profile info
  public function update_profile_info(){
    $query = "UPDATE ". $this->table . " SET user_firstname = :first_name, user_lastname = :last_name, user_email = :email WHERE user_id = :id AND active = 1 AND time_online != 0";
    //prepare statement
    $stmt = $this->conn->prepare($query);
    //make sure id is absolute number
    $this->user_id = abs((int)$this->id);
    //bind data
    $stmt->bindParam(':first_name', $this->first_name);
    $stmt->bindParam(':last_name', $this->last_name);
    $stmt->bindParam(':email', $this->email);
    $stmt->bindParam(':id', $this->user_id);
    //execute query
    if($stmt->execute()){
      //return true if it is updated succesfully
      return true;
    }
      //print error for debugging
      printf("Error: %s.\n", $stmt->error);
      return false;
  }
  // function to check if profile info has been succesfully updated
  public function check_profile_is_updated(){
    $query = "SELECT user_firstname, user_lastname, user_email FROM ". $this->table . " WHERE user_id = :id AND active = 1 AND time_online != 0";
    //prepare statement
    $stmt = $this->conn->prepare($query);
    //make sure id is absolute number
    $this->user_id = abs((int)$this->id);
    //bind data
    $stmt->bindParam(':id', $this->user_id);
    //execute query
    $stmt->execute();
    //return result
    return $stmt;
  }
  //function to delete profile photo
  public function get_profile_pic_name_to_delete_image_from_profile_photo_folder(){
    $query = "SELECT user_image FROM ". $this->table . " WHERE user_id = :id AND active = 1 AND time_online != 0";
    //prepare statement
    $stmt = $this->conn->prepare($query);
    //make sure id is absolute number
    $this->id = abs((int)$this->user_id);
    //bind data
    $stmt->bindParam(':id', $this->id);
    //execute query
    $stmt->execute();
    //return result
    return $stmt;
  }
  //change profile photo
  public function change_profile_pic(){
    $query = "UPDATE ". $this->table . " SET user_image = :new_image WHERE user_id = :id AND active = 1 AND time_online != 0";
    //prepare statement
    $stmt = $this->conn->prepare($query);
    //make sure id is absolute number
    $this->id = abs((int)$this->user_id);
    //bind data
    $stmt->bindParam(':new_image', $this->new_profile_photo);
    $stmt->bindParam(':id', $this->id);
    //execute query
    if($stmt->execute()){
      //return true if it is updated succesfully
      return true;
    }
      //print error for debugging
      printf("Error: %s.\n", $stmt->error);
        return false;
  }
  //function to update profile info on database to null
  public function update_profile_pic_to_null(){
    $query = "UPDATE ". $this->table . " SET user_image = null WHERE user_id = :id AND active = 1 AND time_online != 0";
    //prepare statement
    $stmt = $this->conn->prepare($query);
    //make sure id is absolute number
    $this->id = abs((int)$this->user_id);
    //bind data
    $stmt->bindParam(':id', $this->id);
    //execute query
    if($stmt->execute()){
      //return true if it is updated succesfully
      return true;
    }
      //print error for debugging
      printf("Error: %s.\n", $stmt->error);
        return false;
  }
  //function to check if profilr pic is updated to null
  public function check_if_user_image_is_updated(){
    $query = "SELECT user_image FROM ". $this->table . " WHERE user_id = :id AND active = 1 AND time_online != 0";
    //prepare statement
    $stmt = $this->conn->prepare($query);
    //make sure id is absolute number
    $this->id = abs((int)$this->user_id);
    //bind data
    $stmt->bindParam(':id', $this->id);
    //execute query
    $stmt->execute();
    //return result
    return $stmt;
  }
  //function to delete user account
  public function delete_user_account(){
    $query = "DELETE FROM ". $this->table . " WHERE user_id = :id AND active = 1 AND time_online != 0";
    //prepare statement
    $stmt = $this->conn->prepare($query);
    //make sure id is absolute number
    $this->id = abs((int)$this->user_id);
    //bind data
    $stmt->bindParam(':id', $this->id);
    //execute query
    if($stmt->execute()){
      //return true if it is deleted succesfully
      return true;
    }
      //print error for debugging
      printf("Error: %s.\n", $stmt->error);
        return false;
  }
  //function to check if user account is succesfully deleted
  public function check_user_account_is_deleted(){
    $query = "SELECT user_id FROM ". $this->table . " WHERE user_id = :id AND active = 1 AND time_online != 0";
    //prepare statement
    $stmt = $this->conn->prepare($query);
    //make sure id is absolute number
    $this->id = abs((int)$this->user_id);
    //bind data
    $stmt->bindParam(':id', $this->id);
    //execute query
    $stmt->execute();
    //return result
    return $stmt;
  }
  //function to update time online after logging out
  public function update_time_online_logout(){
    $query = "UPDATE ". $this->table . " SET time_online = 0 WHERE user_id = :id AND active = 1";
    //prepare statement
    $stmt = $this->conn->prepare($query);
    //make sure id is absolute number
    $this->id = abs((int)$this->user_id);
    //bind data
    $stmt->bindParam(':id', $this->id);
    //execute query
    if($stmt->execute()){
      //return true if it is updated succesfully
      return true;
    }
      //print error for debugging
      printf("Error: %s.\n", $stmt->error);
        return false;
  }
  //check if time online is updated
  public function check_time_online_updated(){
    $query = "SELECT time_online FROM ". $this->table . " WHERE user_id = :id AND active = 1";
    //prepare statement
    $stmt = $this->conn->prepare($query);
    //make sure id is absolute number
    $this->id = abs((int)$this->user_id);
    //bind data
    $stmt->bindParam(':id', $this->id);
    //execute query
    $stmt->execute();
    //return result
    return $stmt;
  }

}
