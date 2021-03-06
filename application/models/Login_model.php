<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Login_model extends CI_Model
{
    // This function used to check the login credentials of the user
    function loginMe($email, $password)
    {
        $this->db->select('BaseTbl.userId, BaseTbl.password, BaseTbl.name, BaseTbl.roleId, Roles.role');
        $this->db->from('tbl_admin as BaseTbl');
        $this->db->join('tbl_roles as Roles','Roles.roleId = BaseTbl.roleId');
        $this->db->where('BaseTbl.email', $email);
        $this->db->where('BaseTbl.isdeleted', 0);
        $query = $this->db->get();
        
        $user = $query->row();
        
        if(!empty($user)){
            if(verifyHashedPassword($password, $user->password)){
                return $user;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }
    // This function used to check email exists or not
    function checkEmailExist($email)
    {
        $this->db->select('userId');
        $this->db->where('email', $email);
        $this->db->where('isdeleted', 0);
        $query = $this->db->get('tbl_admin');

        if ($query->num_rows() > 0){
            return true;
        } else {
            return false;
        }
    }
    // This function used to insert reset password data
    function resetPasswordUser($data)
    {
        $result = $this->db->insert('tbl_reset_password', $data);

        if($result) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    // This function is used to get customer information by email-id for forget password email
    function getCustomerInfoByEmail($email)
    {
        $this->db->select('userId, email, name');
        $this->db->from('tbl_admin');
        $this->db->where('isdeleted', 0);
        $this->db->where('email', $email);
        $query = $this->db->get();

        return $query->row();
    }
    // This function used to check correct activation deatails for forget password.
    function checkActivationDetails($email, $activation_id)
    {
        $this->db->select('id');
        $this->db->from('tbl_reset_password');
        $this->db->where('email', $email);
        $this->db->where('activation_id', $activation_id);
        $query = $this->db->get();
        return $query->num_rows();
    }

    // This function used to create new password by reset link
    function createPasswordUser($email, $password)
    {
        $this->db->where('email', $email);
        $this->db->where('isdeleted', 0);
        $this->db->update('tbl_admin', array('password'=>getHashedPassword($password)));
        $this->db->delete('tbl_reset_password', array('email'=>$email));
    }
    // This function used to save login information of user
    function lastLogin($loginInfo)
    {
        $this->db->trans_start();
        $this->db->insert('tbl_last_login', $loginInfo);
        $this->db->trans_complete();
    }
    // This function is used to get last login info by user id
    function lastLoginInfo($userId)
    {
        $this->db->select('BaseTbl.createdDtm');
        $this->db->where('BaseTbl.userId', $userId);
        $this->db->order_by('BaseTbl.id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('tbl_last_login as BaseTbl');

        return $query->row();
    }
}

?>