<?php
namespace App\Http\Controllers\WebInterface;

use App\Http\Controllers\Controller;

class ResponseHandler extends Controller {
  static function ReturnNotPost() {
    $response = array("type"=>"error", "id"=>"1", "attributes"=>array("error_friendly"=>"Request not via POST.", "error_code"=>"not_post"));
    header('Content-Type: application/json');
    echo json_encode($response);
  }

  static function ReturnNoCommand() {
    $response = array("type"=>"error", "id"=>"1", "attributes"=>array("error_friendly"=>"No POST data was given.", "error_code"=>"no_postdata"));
    header('Content-Type: application/json');
    echo json_encode($response);
  }

  static function ReturnInvalidSyntax() {
    $response = array("type"=>"error", "id"=>"1", "attributes"=>array("error_friendly"=>"Invalid JSON syntax.", "error_code"=>"invalid_json"));
    header('Content-Type: application/json');
    echo json_encode($response);
  }

  static function ReturnNotEnoughParameters() {
    $response = array("type"=>"error", "id"=>"1", "attributes"=>array("error_friendly"=>"Not enough parameters.", "error_code"=>"not_enough_params"));
    header('Content-Type: application/json');
    echo json_encode($response);
  }

  static function ReturnUserNotExist() {
    $response = array("type"=>"error", "id"=>"1", "attributes"=>array("error_friendly"=>"The user does not exist.", "error_code"=>"no_user"));
    header('Content-Type: application/json');
    echo json_encode($response);
  }

  static function ReturnUserDisabled() {
    $response = array("type"=>"error", "id"=>"1", "attributes"=>array("error_friendly"=>"The user is disabled.", "error_code"=>"user_disabled"));
    header('Content-Type: application/json');
    echo json_encode($response);
  }

  static function ReturnIncorrectPass() {
    $response = array("type"=>"error", "id"=>"1", "attributes"=>array("error_friendly"=>"Incorrect password.", "error_code"=>"user_incorrectpass"));
    header('Content-Type: application/json');
    echo json_encode($response);
  }

  static function ReturnMfaRequired() {
    $response = array("type"=>"error", "id"=>"1", "attributes"=>array("error_friendly"=>"Multi-Factor is required.", "error_code"=>"user_mfarequired"));
    header('Content-Type: application/json');
    echo json_encode($response);
  }

  static function ReturnMfaDenied() {
    $response = array("type"=>"error", "id"=>"1", "attributes"=>array("error_friendly"=>"Multi-Factor token incorrect.", "error_code"=>"user_mfadenied"));
    header('Content-Type: application/json');
    echo json_encode($response);
  }

  static function ReturnInternalError() {
    $response = array("type"=>"error", "id"=>"1", "attributes"=>array("error_friendly"=>"Internal server error.", "error_code"=>"internal_error"));
    header('Content-Type: application/json');
    echo json_encode($response);
  }

  static function ReturnNotAvailable() {
    $response = array("type"=>"error", "id"=>"1", "attributes"=>array("error_friendly"=>"This endpoint is not available", "error_code"=>"endpoint_notavailable"));
    header('Content-Type: application/json');
    echo json_encode($response);
  }

  static function ReturnInvalidData() {
    $response = array("type"=>"error", "id"=>"1", "attributes"=>array("error_friendly"=>"Invalid data entered.", "error_code"=>"invalid_data"));
    header('Content-Type: application/json');
    echo json_encode($response);
  }

  static function ReturnLoginAccepted($token) {
    $response = array("type"=>"response", "id"=>"1", "attributes"=>array("response_friendly"=>"Login was accepted.", "response_code"=>"user_loginaccepted"), "payload"=>array("token"=>$token));
    header('Content-Type: application/json');
    echo json_encode($response);
  }

  static function ReturnLogout() {
    $response = array("type"=>"response", "id"=>"1", "attributes"=>array("response_friendly"=>"User has been logged out.", "response_code"=>"user_loggedout"));
    header('Content-Type: application/json');
    echo json_encode($response);
  }

  static function ReturnPasswordChanged() {
    $response = array("type"=>"response", "id"=>"1", "attributes"=>array("response_friendly"=>"The password has been changed.", "response_code"=>"user_passchanged"));
    header('Content-Type: application/json');
    echo json_encode($response);
  }

  static function ReturnAccountCreated($token) {
    $response = array("type"=>"response", "id"=>"1", "attributes"=>array("response_friendly"=>"The account has been created.", "response_code"=>"account_created"), "payload"=>array("token"=>$token));
    header('Content-Type: application/json');
    echo json_encode($response);
  }

  static function ReturnAccountDeleted() {
    $response = array("type"=>"response", "id"=>"1", "attributes"=>array("response_friendly"=>"The account has been deleted.", "response_code"=>"user_deleted"));
    header('Content-Type: application/json');
    echo json_encode($response);
  }

  static function ReturnInvalidApiKey() {
    $response = array("type"=>"error", "id"=>"1", "attributes"=>array("error_friendly"=>"Invalid API key.", "error_code"=>"invalid_apikey"));
    header('Content-Type: application/json');
    echo json_encode($response);
  }

  static function ReturnRiskEngineError($incidentid) {
    $response = array("type"=>"error", "id"=>"1", "attributes"=>array("error_friendly"=>"RiskEngine security error.", "error_code"=>"riskengine_error", "incident_id"=>$incidentid));
    header('Content-Type: application/json');
    echo json_encode($response);
  }

  static function ReturnRiskEngineWarning($incidentid, $token) {
    $response = array("type"=>"error", "id"=>"1", "attributes"=>array("error_friendly"=>"RiskEngine security warning.", "error_code"=>"riskengine_warning", "incident_id"=>$incidentid), "payload"=>array("token"=>$token));
    header('Content-Type: application/json');
    echo json_encode($response);
  }
}
?>
