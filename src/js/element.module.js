import Property from '../../src/js/property.module.js';

class Element {
  constructor()
  {
    this.element = null;
    this.type = null;
    this.id = null;
    this.classes = [];
    this.main_element = $("#main");
    this.properties = [];
  }
  update() {
    this.toggleProperties();

    let _element = this.element;

    if($(this.element).attr("id") != undefined) {
      _element = $("#"+$(this.element).attr("id"));
    }

    this.properties.forEach((property,index)=>{
      if(PropertyType.isBackgroundProperty(property.property_type) == true)
      {
        $(_element).css(property.key,'url('+property.value+')');
      }

      if(PropertyType.isPixel(property.property_type) == true)
      {
        $(_element).data(property.key,property.value);
      }

      if(PropertyType.isDataProperty(property.property_type) == true)
      {
        $(_element).data(property.key,property.value);
      }

      if(PropertyType.isAttrProperty(property.property_type) == true)
      {
        $(_element).attr(property.key,property.value);
      }

      if(PropertyType.isFunctionProperty(property.property_type) == true)
      {
        if(property.key == "text")
          $(_element).text(property.value);
        else if(property.key == "val")
          $(_element).val(property.value);
      }

      if(PropertyType.isCssProperty(property.property_type) == true)
      {
        $(_element).css(property.key,PropertyType.getSintaxBefore(property.property_type)+property.value+PropertyType.getSintaxAfter(property.property_type));
      }

      if(PropertyType.isAnimationProperty(property.property_type) == true)
      {
        $(_element).removeClass("animate__animated");
        $(_element).addClass(property.value);
      }

      if(PropertyType.isClassProperty(property.property_type) == true)
      {
        $(_element).removeClass("display-1 display-2 display-3 display-4 h1 h2 h3 h4 h5 h6");
        $(_element).addClass(property.value);
      }

      $("#"+property.key).val(property.value);
    });
  }
  get(key)
  {
    if(this.properties[key] != undefined) {
      return this.properties[key];
    }
  }
  existProperty(key)
  {
    let id = null;

    this.properties.forEach((property,_id)=>{
      if(key == property.key)
      {
        id = _id;
      }
    });

    return id;
  }
  getPropertyByKey(key)
  {
    let index = this.existProperty(key);

    if(index != null)
    {
      return this.getProperty(index);
    }

    return false;
  }
  getProperty(index)
  {
    return this.properties[index];
  }
  set(key,value,property_type)
  {
    let index = this.existProperty(key);
    let property = null;

    if(index != undefined)
    {
      property = this.getProperty(index);
      property.key = key;
      property.value = value;
      property.property_type = property_type;
    } else {
      property = new Property;
      property.key = key;
      property.value = value;
      property.property_type = property_type;
      this.properties.push(property)
    }
  }
  _make(element,is_new_element)
  {
    this.setElement(element);

    $(element).addClass("webflow-item-selected");

    if(is_new_element == true)
    {
      this.insert(element);
    }
  }
  setType(type)
  {
    this.type = type;
  }
  getType()
  {
    return this.type;
  }
  setElement(element)
  {
    this.element = element;
  }
  getElement()
  {
    return this.element;
  }
  insert(element)
  {
    if(WebFlow.getMainElement().element != undefined)
    {
      $(WebFlow.getMainElement().element).append($(element))
    } else {
      if($("#main").length > 0)
      {
        $("#main").append($(element))
      } else if($("#page").length > 0) {
        $("#page").append($(element))
      }
    }
  }
  toggleProperties() {
    $("[data-only_for]").each((key,element)=>{
      let only_for = $(element).data('only_for');

      if(only_for != undefined)
      {
        if(only_for.includes(this.element_type.toLowerCase()))
        {
          $(element).removeClass("d-none");
        } else {
          $(element).addClass("d-none");
        }
      }
    });
  }
  setFollowerSpan()
  {
    let set_at_bottom = false;
    let position = $(this.getElement()).position();

    position.left = parseInt(this.getElement().css("marginLeft")) + position.left + parseInt(this.getElement().css("paddingLeft"));

    if(position.top < (this.getElement().height() + 6))
    {
      set_at_bottom = true;
      position.top = this.getElement().height() + parseInt(this.getElement().css("marginTop")) + parseInt(this.getElement().css("paddingTop")) + parseInt(this.getElement().css("paddingBottom"));
    }

    let div = null;
    let is_new_element = false;

    if($("#follower").length > 0)
    {
      div = $("#follower");
    } else {
      div = $("<div/>").attr("id","follower");
      is_new_element = true;
    }

    // div.css({left:position.left,top:position.top}).text("<"+this.type.toLowerCase()+">").addClass("webflow-item-selected-span");

    if(is_new_element == true) $("#main").append(div);
  }
}
export default Element;