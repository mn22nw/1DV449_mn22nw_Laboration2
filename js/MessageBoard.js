var MessageBoard = {

    messages: [],
    messageIdArray: [],
    textField: null,
    messageArea: null,
	firstCheck: false,
	timer:false,
    init:function(e)
    {
		
		    MessageBoard.textField = document.getElementById("inputText");
		    MessageBoard.nameField = document.getElementById("inputName");
            MessageBoard.messageArea = document.getElementById("messagearea");
            //Get messages 
            if (MessageBoard.messages.length === 0) { console.log("den var 0");
            	
            	MessageBoard.getMessages();
           
            }
   
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
    getMessages:function() {
        console.log("INNE");
        $.ajax({
			type: "GET",
			url: "functions.php",
			data: {function: "getMessages"}
		}).done(function(data) { // called when the AJAX call is ready
			MessageBoard.clearMessageArea();			
			data = JSON.parse(data);
	
			for(var mess in data) {
				var obj = data[mess];
			    var text = obj.name +" said:\n" +obj.message;
			    var messageID = obj.serial;
				var mess = new Message(text, new Date(), messageID);
				MessageBoard.messageIdArray.push(messageID);
                MessageBoard.messages.push(mess);
               
                if( MessageBoard.messages.length > 0) {
            	
            		MessageBoard.renderMessage(mess);
          	  }
			}			
			document.getElementById("nrOfMessages").innerHTML = MessageBoard.messages.length;
		});
    },
    
    getNewMessages:function(){  
    	//check here if it already exists and only render message if it's new. 
      console.log("newMessages");
        $.ajax({
			type: "GET",
			url: "functions.php",
			data: {function: "getMessages"}
		}).done(function(data) { // called when the AJAX call is ready
						
			data = JSON.parse(data);
			console.log("newMessages sucess");
			for(var mess in data) {
				var obj = data[mess];
			    var text = obj.name +" said:\n" +obj.message;
			    var messageID = obj.serial;
				var mess = new Message(text, new Date(), messageID);

				//check if message exists in array.
				
				if ($.inArray(messageID, MessageBoard.messageIdArray) === -1) 
				{
					console.log("inte i array!");
					//pusha in den i array och rendera ut den!
					MessageBoard.messageIdArray.push(messageID);
               		MessageBoard.messages.push(mess);
               		MessageBoard.renderMessage(mess);
				}
			}
			
			document.getElementById("nrOfMessages").innerHTML = MessageBoard.messages.length;
		});
    	//need to get messages 
    	var number = document.getElementById("nrOfMessages").innerHTML;
 
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
		  console.log(data);
		  //clear inputs
    	  MessageBoard.textField.value = "";
    	  MessageBoard.nameField.value = "";
		 
		  MessageBoard.getNewMessages();	
		});
	    
    } ,
    clearMessageArea: function(){
        // Remove all messages
        MessageBoard.messageArea.innerHTML = "";
     
    	//clear inputs
    	 MessageBoard.textField.value = "";
    	 MessageBoard.nameField.value = "";
    	
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
    longPolling: function(url){
		var checkUrl="db.db";

		if(MessageBoard.timer !== false) {	
		clearInterval(MessageBoard.timer);}

			MessageBoard.getNewMessages();

		var pageLoad = new Date().getTime();
	    MessageBoard.timer = setInterval(function(){ MessageBoard.checkForUpdate(checkUrl, pageLoad);}, 5000);
	},
	
   checkForUpdate: function(checkUrl, pageLoad) {  
        $.ajax(checkUrl, {
            type : 'HEAD',
            success : function (response, status, xhr) {  
                // if the server omits the 'Last-Modified' header
                // the following line will return 0. meaning that
                // has not updated. you may refine this behaviour...
                var lastModified = new Date(xhr.getResponseHeader('Last-Modified'))
                    .getTime();                 
                if(lastModified > pageLoad) {
                   //databasen har modifierats!
                   console.log("modified");
                  MessageBoard.longPolling();
                }
            }
        }); 
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