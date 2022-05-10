<?php defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' ); 
class FrontController extends CI_Controller {
	protected $role = '';
	protected $vendorId = '';
	protected $name = '';
	protected $roleText = '';
	protected $global = array ();
	protected $lastLogin = '';
	protected $salary = '';
	
	// Takes mixed data and optionally a status code, then creates the response
	public function response($data = NULL) {
		$this->output->set_status_header ( 200 )->set_content_type ( 'application/json', 'utf-8' )->set_output ( json_encode ( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) )->_display ();
		exit ();
	}
	// This function used to check the user is logged in or not
	function is_signed_in() {
		$is_signed_in = $this->session->userdata ( 'is_signed_in' );
		
		if (! isset ( $is_signed_in ) || $is_signed_in != TRUE) {
			redirect ( 'signin' );
		} else {
			redirect ( 'dashboard' );
		}
	}
	// This function is used to check the access
	function isAdmin() {
		if ($this->role != ROLE_ADMIN) {
			return true;
		} else {
			return false;
		}
	}
	// This function is used to check the access
	function isTicketter() {
		if ($this->role != ROLE_ADMIN || $this->role != ROLE_MANAGER) {
			return true;
		} else {
			return false;
		}
	}
	// This function is used to load the set of views
	function loadThis() {
		$this->global ['pageTitle'] = 'Admin : Access Denied';
		
		$this->load->view ( 'includes/header', $this->global );
		$this->load->view ( 'access' );
		$this->load->view ( 'includes/footer' );
	}
	// This function is used to logged out user from system
	function logout() {
		$this->session->sess_destroy ();
		
		redirect ( 'login' );
	}

	// This function used to load views
    function loadViews($viewName = "", $headerInfo = NULL, $pageInfo = NULL, $footerInfo = NULL){

        $this->load->view('includes/header', $headerInfo);
        $this->load->view($viewName, $pageInfo);
        $this->load->view('includes/footer', $footerInfo);
    }
	// This function used provide the pagination resources
	function paginationCompress($link, $count, $perPage = 10, $segment = SEGMENT) {
		$this->load->library ( 'pagination' );

		$config ['base_url'] = base_url () . $link;
		$config ['total_rows'] = $count;
		$config ['uri_segment'] = $segment;
		$config ['per_page'] = $perPage;
		$config ['num_links'] = 5;
		$config ['full_tag_open'] = '<nav><ul class="pagination">';
		$config ['full_tag_close'] = '</ul></nav>';
		$config ['first_tag_open'] = '<li class="arrow" style="display:none;">';
		$config ['first_link'] = 'First';
		$config ['first_tag_close'] = '</li>';
		$config ['prev_link'] = 'Previous';
		$config ['prev_tag_open'] = '<li class="arrow prev">';
		$config ['prev_tag_close'] = '</li>';
		$config ['next_link'] = 'Next';
		$config ['next_tag_open'] = '<li class="arrow next">';
		$config ['next_tag_close'] = '</li>';
		$config ['cur_tag_open'] = '<li class="active listing" style="display:none;"><a href="#">';
		$config ['cur_tag_close'] = '</a></li>';
		$config ['num_tag_open'] = '<li class="listing" style="display:none;">';
		$config ['num_tag_close'] = '</li>';
		$config ['last_tag_open'] = '<li class="arrow" style="display:none;">';
		$config ['last_link'] = 'Last';
		$config ['last_tag_close'] = '</li>';
	
		$this->pagination->initialize ( $config );
		$page = $config ['per_page'];
		$segment = $this->uri->segment ( $segment );
	
		return array (
				"page" => $page,
				"segment" => $segment
		);
	}
}