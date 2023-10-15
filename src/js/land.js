$(document).ready(function(){
	let user = new User;

	dinamicLoader.showLoader($(".response"));

  window.setName = function(element,event,next_element)
  {
    let name = $(element).val();

    if(name)
    {
      user.setName(name);

      $("#name").html(translate("Bienvenido")+" "+user.getName());

      checkStep();
      $("#title-first").removeClass("display-3").addClass("text-muted display-5");
      $(element).addClass("is-valid").removeClass("is-invalid");
    } else {
      $("#proyect-name").text("");
      $("#title-first").removeClass("display-5 text-muted").addClass("display-3");
      $(element).addClass("is-invalid").removeClass("is-valid");
    }
      
    nextElement(event,next_element);
  }

  window.setLastName = function(element,event,next_element)
  {
    let last_name = $(element).val();

    if(last_name)
    {
      user.setLastName(last_name);

      checkStep();
      $("#title-first").removeClass("display-3").addClass("text-muted display-5");
      $(element).addClass("is-valid").removeClass("is-invalid");
    } else {
      $("#proyect-name").text("");
      $("#title-first").removeClass("display-5 text-muted").addClass("display-3");
      $(element).addClass("is-invalid").removeClass("is-valid");
    }
      
    nextElement(event,next_element);
  }

  window.setEmail = function(element,event,next_element)
  {
    let email = $(element).val();

    if(email)
    {
      user.setEmail(email);

      checkStep();
      $("#title-first").removeClass("display-3").addClass("text-muted display-5");
      $(element).addClass("is-valid").removeClass("is-invalid");
    } else {
      $("#proyect-name").text("");
      $("#title-first").removeClass("display-5 text-muted").addClass("display-3");
      $(element).addClass("is-invalid").removeClass("is-valid");
    }
      
    nextElement(event,next_element);
  }

  window.setPassword = function(element,event,next_element)
  {
    let password = $(element).val();

    if(password)
    {
      user.setPassword(password);

      checkStep();
      $("#title-first").removeClass("display-3").addClass("text-muted display-5");
      $(element).addClass("is-valid").removeClass("is-invalid");
    } else {
      $("#proyect-name").text("");
      $("#title-first").removeClass("display-5 text-muted").addClass("display-3");
      $(element).addClass("is-invalid").removeClass("is-valid");
    }
      
    nextElement(event,next_element);
  }

  window.setRePassword = function(element,event,next_element)
  {
    let re_password = $(element).val();

    if(re_password)
    {
      user.setRePassword(re_password);

      checkStep();
      $("#title-first").removeClass("display-3").addClass("text-muted display-5");
      $(element).addClass("is-valid").removeClass("is-invalid");
    } else {
      $("#proyect-name").text("");
      $("#title-first").removeClass("display-5 text-muted").addClass("display-3");
      $(element).addClass("is-invalid").removeClass("is-valid");
    }
      
    nextElement(event,next_element);
  }
  


  window.nextElement = function(event,next_element)
  {
    nextElement(event,next_element)
  }

  function nextElement(event,next_element)
  {
      console.log(next_element)
    if(event.keyCode == 13)
    {
      $(next_element).focus();
    }
  }


  window.setPrivateProyect = function(element)
  {
    if($(element).is(":checked")){
      user.setPrivate(true);
    } else {
      user.setPrivate(false);
    }

    $("#content-wrapper").toggleClass("private-proyect-bg");
  }

  window.setProyectDescription = function(element,event,next_element)
  {
    let proyect_description = $(element).val();
    
    user.setProyectDescription(proyect_description);

    checkStep();

    nextElement(event,next_element)
  }

  window.setProyectName = function(element,event,next_element)
  {
    let proyect_name = $(element).val();

    if(proyect_name)
    {
      user.setProyectName(proyect_name);

      $("#proyect-name").text(user.getProyectName());

      checkStep();
      $("#title-first").removeClass("display-3").addClass("text-muted display-5");
      $(element).addClass("is-valid").removeClass("is-invalid");
    } else {
      $("#proyect-name").text("");
      $("#title-first").removeClass("display-5 text-muted").addClass("display-3");
      $(element).addClass("is-invalid").removeClass("is-valid");
    }
      
    nextElement(event,next_element)
  }
  
  window.checkEmail = function(element,event,next_element)
  {
    let email = $(element).val();

    nextElement(event,next_element);

    if(isValidMail(email))
    {
      user.setEmail(email);
      $(element).addClass("is-valid").removeClass("is-invalid");
    } else {
      $(element).addClass("is-invalid").removeClass("is-valid");
    }
  }

  window.nextElement = function(event,next_element)
  {
    nextElement(event,next_element)
  }

  function nextElement(event,next_element)
  {
    if(event.keyCode == 13)
    {
      $(next_element).focus();
    }
  }

  function checkStep()
  {
    if(isEmpty(user.getName()))
    {
      $("#sign").attr("disabled",true);
      return false;
    }

    if(isEmpty(user.getLastName()))
    {
      $("#sign").attr("disabled",true);

      return false;
    }

    if(isEmpty(user.getEmail()))
    {
      $("#sign").attr("disabled",true);

      return false;
    }

    if(isEmpty(user.getPassword()))
    {
      $("#sign").attr("disabled",true);

      return false;
    }

    if(isEmpty(user.getRePassword()))
    {
      $("#sign").attr("disabled",true);

      return false;
    }

    if(user.getPassword() != user.getRePassword())
    {
      $("#sign").attr("disabled",true);

      return false;
    }
    
    $("#sign").removeAttr("disabled");
  }
});

class User extends Http {
  constructor()
  {
    super();    
    this.steps = 4;
    this.name = null;
    this.last_name = null;
    this.email = null;
    this.password = null;
    this.re_password = null;
  }
  getName()
  {
    return this.name;
  }
  setName(name)
  {
    this.name = name;
  }
  getLastName()
  {
    return this.last_name;
  }
  setLastName(last_name)
  {
    this.last_name = last_name;
  }
  getEmail()
  {
    return this.email;
  }
  setEmail(email)
  {
    this.email = email;
  }
  getPassword()
  {
    return this.password;
  }
  setPassword(password)
  {
    this.password = password;
  }
  getRePassword()
  {
    return this.re_password;
  }
  setRePassword(re_password)
  {
    this.re_password = re_password;
  }
  saveUser(callback,data){
    return this.call("../../app/application/save_user.php",data,callback);
  }
};