import Axios from "axios";

export default {
    state: {  
      userList:[],
      userMessage:[],
      activeunreadcoun:''
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
    state.userMessage = payload.data;
      setTimeout(function(){
        if(!payload.chat_start)
        {
        var unreadHeight = 0;
        for(var i=1;i <= state.activeunreadcoun ; i++){
                             var unreadHeight = unreadHeight + jQuery('.unread').eq(Number(-i)).prop("scrollHeight")+73;
                        }
            if(!unreadHeight){
                unreadHeight = 0;
            }
                   var chatWindow = document.getElementById('message-wrap'); 
                         var xH = chatWindow.scrollHeight - unreadHeight; 
                         chatWindow.scrollTo(xH, xH);
      }
    },500);
        
        return state.userMessage;
    },
    userSort(state,payload){
         state.userList=_.sortBy(state.userList, [(contact) => {
            if (contact.id == payload) {
                return Infinity;
            }

            //return contact.unread;
        }]);
    },
    psuedoMessageAdd(state,payload){
        state.userMessage.messages.push({
            created_at:new Date(),
            from:authuser.id,
            id:'psuedoid',
            message:payload,
            read:0,
            to:state.userMessage.user.id,
            type:0,
            updated_at:new Date(),
            
        })
    },
    activeunreadcoun(state,payload){
        state.activeunreadcoun = payload;
    }
  },
  actions: { 
    userList(context){
        Axios.get('/userlist')
        .then(response=>{
          context.commit("userList",response.data);
        })
    },
    userMessage(context,payload){
       
      Axios.get('/usermessage/'+payload.userId)
      .then(response=>{
        context.commit("userMessage",{data:response.data,chat_start:payload.chat_start});
        
      })
    },
    userSort(context,payload){
        context.commit("userSort",payload);
    },
    psuedoMessageAdd(context,payload){
        context.commit("psuedoMessageAdd",payload);
    },
    activeunreadcoun(context,payload){
        context.commit("activeunreadcoun",payload);
    }

   },
  getters: {
    userList(state){
      return state.userList


     },
     userMessage(state){
       return state.userMessage
     }
    }
}