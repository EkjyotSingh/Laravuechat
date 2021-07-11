import Axios from "axios";

export default {
    state: {  
      userList:[],
      userMessage:[],
      activeunreadcoun:'',
      infiniteLoaderResseter:1,
    },
  mutations: {  
    userList(state,payload){
        return state.userList = payload.sort(function (a, b) {
        if (a.unread === b.unread) {
            return 0;
        }
        else {
            return (a.unread > b.unread) ? -1 : 1;
        }
    });
    },

    userMessage(state,payload){
        if(payload.page){
            for(var i=0;i<payload.data.messages.length;i++){
                state.userMessage.messages.unshift(payload.data.messages[i]);
            }
        }else{
            state.userMessage = payload.data;
        }
        if(payload.dispacher != 'loader')
        ////if dispatched from loader no need to scroll to any posiiton//////
        {
            setTimeout(()=>{
                if(!payload.chat_start){
                    var unreadHeight = 0;
                    for(var i=0;i < Number(state.activeunreadcoun) ; i++){
                        var unreadHeight = unreadHeight + jQuery('.unread').eq(i).prop("scrollHeight")+73;
                    }
                    if(!unreadHeight){
                        unreadHeight = 0;
                    }
                    var chatWindow = document.getElementById('message-wrap');
                    var xH = chatWindow.scrollHeight - unreadHeight;
                    chatWindow.scrollTo(xH, xH);
                    state.activeunreadcoun = '';
                    
                }
            },500);
        }
        jQuery('.loader-container').css('display','none');
        return state.userMessage;
    },
    userSort(state,payload){
        state.userList=_.sortBy(state.userList, [(contact) => {
            if (contact.id == payload) {
                return Infinity;
            }
        }]);
    },
    psuedoMessageAdd(state,payload){
        state.userMessage.messages.push({
            created_at:new Date(),
            from:authuser.id,
            id:'psuedoid-'+payload.psuedoId,
            message:payload.message,
            read:0,
            to:state.userMessage.user.id,
            type:0,
            updated_at:new Date(),
            
        })
    },
    activeunreadcoun(state,payload){
        state.activeunreadcoun = payload;
    },
    psuedoMessageDelete(state,payload){
        if(!payload.messageId){
            state.userMessage.messages=[]; 

            state.userList=state.userList.filter((user)=>{
                    if(state.userMessage.user.id == user.id)
                    {
                        user.latestMessage = '';
                        user.latestMessageId = '';
                    }
                return user;
            });
        }else{
            state.userMessage.messages = state.userMessage.messages.filter((message)=>{
                if(message.id != payload.messageId){
                    return message;
                }
                return '';
            });
            if(payload.userMessageId == payload.messageId)
            {
                state.userList=state.userList.filter((user)=>{
                    if(user.id == state.userMessage.user.id)
                    { 
                        if(state.userMessage.messages.length - 1 >=0)
                        {
                            user.latestMessage = state.userMessage.messages[state.userMessage.messages.length - 1].message;
                            user.latestMessageId = state.userMessage.messages[state.userMessage.messages.length - 1].id;
                            return user;
                        }else{
                            user.latestMessage = '';
                            user.latestMessageId = '';
                            return user;
                        }
                    }
                    return user;
                });
                
            }
        }       
    },
    messageAdd(state,payload){
        state.userMessage.messages = state.userMessage.messages.filter((message)=>{
            if(message.id == 'psuedoid-'+payload.psuedoId){
                message.id = payload.id;
                message.read = payload.read;
                message.created_at = payload.created_at;
                return message;
            }
            return message;
        });
        state.userList=state.userList.filter((user)=>{
            if(user.id == state.userMessage.user.id)
            { 
                if(state.userMessage.messages.length - 1 >=0)
                {
                    user.latestMessage = state.userMessage.messages[state.userMessage.messages.length - 1].message;
                    user.latestMessageId = state.userMessage.messages[state.userMessage.messages.length - 1].id;
                    return user;
                }else{
                    user.latestMessage = '';
                    user.latestMessageId = '';
                    return user;
                }
            }
            return user;
        });
    },
    messageReceive(state,payload){
        var m = payload.message;
        state.userMessage.messages.push(m);
        state.userList=state.userList.filter((user)=>{
            if(user.id == state.userMessage.user.id)
            {
                user.latestMessage = state.userMessage.messages[state.userMessage.messages.length - 1].message;
                user.latestMessageId = state.userMessage.messages[state.userMessage.messages.length - 1].id;
                user.unread = 0;
                return user;
            }
            return user;
        });
    },
    infiniteLoaderResseter(state,payload){
        if(payload == 'first'){
        return state.infiniteLoaderResseter = 1;
        }else{
            state.infiniteLoaderResseter++;
        }
    }
},
  actions: { 
      //userlist fetch///
    userList(context){
        Axios.get('/userlist')
        .then(response=>{
          context.commit("userList",response.data);
        })
    },
        ////usermessage fetch////
    userMessage(context,payload){
        var limit = 5;
        limit = (Number(context.state.activeunreadcoun)>limit -3) ? Number(context.state.activeunreadcoun) + limit : Number(limit) ;
        return new Promise((resolve, reject) => {
                Axios.get('/usermessage/'+payload.userId+'/'+payload.page+'/'+limit)
                .then(response=>{
                    context.commit("userMessage",{data:response.data,chat_start:payload.chat_start,page:payload.page,dispacher:payload.dispacher});
                    resolve(limit - response.data.messages.length > 0 ? 'completed' : 'havemoredata');
                },error => {
                    reject(error);
                })
    })
    
    },
        //not used anywhere in web app but for future use if we want to make selected user at top of userlist////
        userSort(context,payload){
            context.commit("userSort",payload);
        },
        ////when message sent then upto the time response doesnot recieved this adds  message to chatarea////
        psuedoMessageAdd(context,payload){
            context.commit("psuedoMessageAdd",payload);
        },
        activeunreadcoun(context,payload){
            context.commit("activeunreadcoun",payload);
        },
        ////when message response recieved this takes care of message id supplied to deletemessage popup////
        psuedoMessageDelete(context,payload){
            context.commit("psuedoMessageDelete",payload);
        },
        ////adds original message and replace psuedomessage after getting response//////
        messageAdd(context,payload){
            context.commit("messageAdd",payload);
        },
        ////works during send message listening event triggers after recieving message this action adds message in chatarea/////
        messageReceive(context,payload){
            context.commit("messageReceive",payload);
        },
        /////with this 1st time infinite loader is invisible depending on payload it is making infinite loader visible////
        infiniteLoaderResseter(context,payload){
            context.commit("infiniteLoaderResseter",payload);
        }
    },
    getters: {
        userList(state){
            return state.userList
        },
        userMessage(state){
            return state.userMessage
        },
        infiniteLoaderResseter(state){
            return state.infiniteLoaderResseter
        }
    }
}