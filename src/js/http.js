const GET = "GET";
const POST = "POST";

class Http 
{ 
  async call(url,data,callback,loader,delay,METHOD,async)
  {
    const boxLoader = new BoxLoader(delay);

    boxLoader.setLoader().then(()=>{
      $.ajax({
        async : async != undefined ? async : true,
        url : url,
        data : data,
        type : METHOD,
        dataType : "json",
        crossDomain : true,
        beforeSend : ()=> {
        },
        complete : (data) => {
          boxLoader.closeLoader().then(()=>{
            if(data.responseJSON != undefined) {
              callback(data.responseJSON);
            } else if (data.responseText != undefined)  {
              data.responseJSON = JSON.parse(data.responseText);
            }
          });
        }
      });
    });
  }
  async singleCall(url,data,callback,loader,timeOut,METHOD)
  {
    let boxLoader = new BoxLoader;
        loader = loader != undefined ? loader : true;
        timeOut = timeOut != undefined ? timeOut : true;

    let options = {
      "async" : true,
      "url" : url,
      "loader" : loader,
      "timeOut" : 1000,
      "method" : METHOD,
      "dataType" : "json",
      "crossDomain" : true,
      "loadOldObject" : true,
      "data": data,
    };

    const result = await boxLoader.setLoader();

    return $.ajax({
        url : options.url,
        data : options.data,
        type : options.method,
        dataType : options.dataType,
        crossDomain : options.crossDomain,
        beforeSend : ()=> {
        },
        complete : (data) => {
        }
    });
  }
  callFile(url,data,callback,progress,timeOut) 
  {
    let boxLoader = new BoxLoader;

    let _timeOut = timeOut != undefined ? timeOut : true;

    let options = {
      "async" : true,
      "url" : url,
      "timeOut" : 0,
      "method" : 'POST',
      "dataType" : "json",
      "crossDomain" : true,
      "loadOldObject" : true,
      "data": data,
    };

    $.ajax({
        xhr: function() {
            var xhr = new window.XMLHttpRequest();
            
            xhr.upload.addEventListener("progress", function(evt) {
                if (evt.lengthComputable) {
                    var percentComplete = ((evt.loaded / evt.total) * 100);
                    $(progress).width(percentComplete + '%');
                    $(progress).html(percentComplete+'%');
                }
            }, false);

            return xhr;
        },
        url: url,
        type: options.method,
        data: options.data,
        cache: false,
        dataType: options.dataType,
        processData: false, 
        contentType: false, 
        success: (data, textStatus, jqXHR)=> {
          callback(data);
        },
        error: (jqXHR, textStatus, errorThrown) => {
        }
    });
  }
}