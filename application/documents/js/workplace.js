var timeOut = null;
var activityMade = function(){

clearInterval(timeOut); //first clears the interval
  timeOut = setInterval(

    function()
    { 
        location.href = location.href;
    }
      
      , 180000); 
  //logs to the console at every 3 seconds of inactivity
}

var bindEvents = function(){
  var body = document.body;
  // bind click move and scroll event to body
  body.addEventListener("click", activityMade);
  body.addEventListener("mousemove", activityMade);
  body.addEventListener("scroll", activityMade);
  activityMade(); // assume activivity has done at page init
}
bindEvents();