function sendAjaxRequest(urlName,dataMsg,datatype,successFunction)
  {
    $.ajax({
      url: urlName,
      method:"POST",
      data: dataMsg,
      dataType: datatype,
      success: function(msg)
      {
        if(successFunction!="")
         eval(successFunction)(msg);
      }
    });
  }