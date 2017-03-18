function submitQuery() {
  var callback = document.forms["metaform"]["callback"].value;
  var username = document.forms["metaform"]["username"].value;
  var password = document.forms["metaform"]["password"].value;
  var mfatoken = document.forms["metaform"]["mfatoken"].value;
  $.post('login/post', {username: username, password: password, mfatoken: mfatoken}, function(returnedData) {
    var json_payload = JSON.parse(returnedData);
    if (json_payload !== null) {
      if (json_payload['type'] == "error") {
        if (json_payload['attributes']['error_code'] == "riskengine_error") {
          document.getElementById("re_error_ic").innerHTML = window.btoa(returnedData);
          $('#re_error').modal('show');
        } else if (json_payload['attributes']['error_code'] == "riskengine_warning") {
          $('#re_warning').modal('show');
          document.getElementById("token").value = json_payload['payload']['token'];
        } else if (json_payload['attributes']['error_code'] == "user_mfarequired") {
          $('#re_challenge').modal('show');
        } else {
          document.getElementById("incident_id").value = window.btoa(returnedData);
          document.getElementById("callback_error_form").submit();
        }
      } else if (json_payload['type'] == "response" && json_payload['attributes']['response_code'] == "user_loginaccepted"){
        document.getElementById("token").value = json_payload['payload']['token'];
        document.getElementById("callback_form").submit();
      } else {
        document.getElementById("error_ic").innerHTML = window.btoa(returnedData);
        $('#error_modal').modal('show');
      }
    } else {
      document.getElementById("error_ic").innerHTML = window.btoa(returnedData);
      $('#error_modal').modal('show');
    }
  });
}
