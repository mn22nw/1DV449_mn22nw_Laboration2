var MessageBoard = {

    messages: [],
    textField: null,
    messageArea: null,
	firstCheck: false,
	timer:false,
    init:function(e)
    {
		
		    MessageBoard.textField = document.getElementById("inputText");
		    MessageBoard.nameField = document.getElementById("inputName");
            MessageBoard.messageArea = document.getElementById("messagearea");
   
            // Add eventhandlers    
            document.getElementById("inputText").onfocus = function(e){ this.className = "focus"; };
            document.getElementById("inputText").onblur = function(e){ this.className = "blur"; };
            document.getElementById("buttonSend").onclick = function(e) {MessageBoard.sendMessage(); return false;};
            MessageBoard.textField.onkeypress = function(e){ 
                                                    if(!e) var e = window.event;
                                                    
                                                    if(e.keyCode == 13 && !e.shiftKey){
                                                        MessageBoard.sendMessage(); 
                                                       
                                                        return false;
                                                    }
                                               };
    
    },

    getMessages:function(lastTime){
    	console.log( "inne"  + lastTime);
                var t = this;
                var latest = null;
                $.ajax({
                		type: 'post',
                        url: 'functions.php',
                        data: {mode: 'get', action: "getMessages", lastTime:  lastTime},
                        dataType:'json',
                        timeout: 30000,
                    //    async: true,
                        cache: false,
                        success: function(data){
                                MessageBoard.clearMessageArea();	
                                		
								for(var mess in data) {
									var obj = data[mess];
									console.log(obj.insertDate);
									
								    var text = obj.name +" said:\n" +obj.message;
								    	latest = obj.insertDate;
									var mess = new Message(text, latest);
					                
					                MessageBoard.messages.push(mess);
					                MessageBoard.renderMessage(mess);				          	  	
								}	
								document.getElementById("nrOfMessages").innerHTML = MessageBoard.messages.length;

					       },
						error: function(e){
							    console.log("error: " + e.responseText );
						},
						complete: function(){
						MessageBoard.getMessages(latest);
						
			 }
                });
     },
    sendMessage:function(){
        
        if(MessageBoard.textField.value == "") return;
        
        
        //Generate and check token
        var token = MessageBoard.changeToken();
        
        // Make call to ajax
        $.ajax({
			type: "GET",
		  	url: "functions.php",
		  	data: {function: "add", name: MessageBoard.nameField.value, message:MessageBoard.textField.value, csrf_token: token}
		}).done(function(data) {
		  console.log(data + " Posted a message");
		  //clear inputs
    	 MessageBoard.textField.value = "";
    	 MessageBoard.nameField.value = "";
		  
		});
	    
    } ,
    clearMessageArea: function(){
        // Remove all messages
        MessageBoard.messageArea.innerHTML = "";
        MessageBoard.messages = [];
    	
        document.getElementById("nrOfMessages").innerHTML = MessageBoard.messages.length;
    },
    renderMessage: function(mess){
    	
        // Message div
        var div = document.createElement("div");
        div.className = "message";
       
        // Clock button
        aTag = document.createElement("a");
        aTag.href="#";
        aTag.onclick = function(){
			MessageBoard.showTime(mess.getMessageId());
			return false;			
		};
        
        var imgClock = document.createElement("img");
        imgClock.src="pic/clock.png";
        imgClock.alt="Show creation time";
        
        aTag.appendChild(imgClock);
        div.appendChild(aTag);
       
        // Message text
        var text = document.createElement("p");
        text.innerHTML = mess.getHTMLText();        
        div.appendChild(text);
            
        // Time - Should fix on server!
        var spanDate = document.createElement("span");
        spanDate.appendChild(document.createTextNode(mess.getDateText()));

        div.appendChild(spanDate);        
        
        var spanClear = document.createElement("span");
        spanClear.className = "clear";

        div.appendChild(spanClear);        

        MessageBoard.messageArea.insertBefore(div, MessageBoard.messageArea.firstChild);       
    },
    removeMessage: function(messageID){
		if(window.confirm("Vill du verkligen radera meddelandet?")){
        
			MessageBoard.messages.splice(messageID,1); // Removes the message from the array.
        
			MessageBoard.renderMessages();
        }
    },
    showTime: function(messageID){
         
         var time = MessageBoard.messages[messageID].getDate();
         
         var showTime = "Created "+time.toLocaleDateString()+" at "+time.toLocaleTimeString();

         alert(showTime);
    },
   
	changeToken: function() {
		var token = "";
		
		 $.ajax({
			type: "GET",
		  	url: "src/helper/nocsrf.php",
		  	data: {function: "generateToken"}
		}).done(function(data) {
				console.log(data);
                token = data;            
        });
        
		return token;
	}
}
	  
window.onload = MessageBoard.init;