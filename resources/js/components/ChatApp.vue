<template>
    <div class="containerr">
        <selecteduserloader/>
        <sidebarr 
        :appUrl="appUrl" 
        :appName="appName"  
        :profileImagePath="profileImagePath"  
        :online="online" 
        :typing="typing"/>
        <div class="content">
            <header>
            <a :href="profileImagePath+userMessage.user.image"  v-if="userMessage.user" target="_blank"><img :src="profileImagePath+userMessage.user.image" onerror="this.onerror=null;this.src=noPersonImage" alt=""></a>
            <div class="info" v-if="userMessage.user">
                <div>
                <span class="user">{{userMessage.user.name}}</span>
                <div v-if="!(typing && (typing.id == userMessage.user.id))">
                    <span class="time" v-if="onlineUser(userMessage.user.id) || online.id == userMessage.user.id">online</span>
                    <span class="time" v-else>offline</span>
                </div>
                <div v-else>
                     <span class="time">typing...</span>
                </div>
                </div>
                <div>
                    <button class="delete_button" @click="allDeleteMessages" :disabled="userMessage.messages.length == 0"  :style="userMessage.messages.length == 0 ? 'color:#ff00005c;' : 'color: red;'" :title="'Delete all chat with '+userMessage.user.name"><i class="fa fa-trash"></i></button>
                    <span  role="button" data-toggle="dropdown" id="navbarDropdown"  aria-haspopup="true" aria-expanded="false" v-pre><i class="fa fa-cog" aria-hidden="true"></i></span>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" :href="profileUpdate">Profile Update</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" :href="logout"
                            onclick="event.preventDefault();document.getElementById('logout-form').submit();"> Logout</a>
                        <form id="logout-form" :action="logout" method="POST" style="display: none;"></form>
                    </div>
                </div>
            </div>
            <div v-else style="margin-right:auto; display:flex;justify-content:space-between;align-items:center;flex:1;"><h3 class="user">Conversations</h3>
            <span  role="button" data-toggle="dropdown" id="navbarDropdown" aria-haspopup="true" aria-expanded="false" v-pre><i class="fa fa-cog" aria-hidden="true"></i></span>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" :href="profileUpdate">Profile Update</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" :href="logout"
                    onclick="event.preventDefault();document.getElementById('logout-form').submit();"> Logout</a>
                <form id="logout-form" :action="logout" method="POST" style="display: none;"></form>
            </div>
            </div>
            <div class="open">
                <a href="javascript:void(0)" @click="openCoversations" title="Select Conversation" style="margin-left: 20px;font-size:22px;color: #5d6a75;"><i class="fa fa-users"></i></a>
            </div>
            </header>
            <div style="height:calc(100vh - 126px );">
            <div v-if="userMessage.messages" id="message-wrap" class="message-wrap" >
            <infinite-loading :identifier="userMessage.user.id + new Date()"  spinner="waveDots" direction="top" @infinite="infiniteHandler" >
                <span slot="no-more"></span>
                 <div slot="no-results"></div>
                 <div slot="error" slot-scope="{ trigger }">
                    Error message, click <a href="javascript:;" @click="trigger">here</a> to retry
                </div>
            </infinite-loading>
            <div class="message-list"  v-for="message in userMessage.messages" :key="message.id" :id="'message'+( message.to == authuserr.id ? (message.read == 0 ? '-unread' :'-read') : '')+'-'+message.id" :ref="'message-'+message.id" v-bind:class="[message.to == userMessage.user.id ? 'me' : 'offerer' ]">
                <div class="single-message-delete-container">
                    <span v-if="message.from != userMessage.user.id" @click.prevent="deleteSingleIdChange(message.id,message.from)" class="single-message-delete-button-left"><i class="fa fa-trash-o"></i></span>
                    <div class="msg" v-bind:class="[message.to == authuserr.id ? (message.read == 0 ? 'unread': '') : '']">{{message.message}}</div>
                    <span v-if="message.from == userMessage.user.id" @click.prevent="deleteSingleIdChange(message.id,message.from)" class="single-message-delete-button-right"><i class="fa fa-trash-o"></i></span>
                </div>
                <div class="time">{{message.created_at | timeformat}} 
                    <span v-if="(message.from == authuserr.id &&  /psuedoid/.test(message.id))"><i class="fa fa-clock-o"></i></span>
                    <span v-if="(message.from == authuserr.id && !/psuedoid/.test(message.id) && message.read == 0)"><i class="fa fa-check unread_tick"></i></span>
                    <span v-if="(message.from == authuserr.id && !/psuedoid/.test(message.id) && message.read != 0)"><i class="fa fa-check read_tick"></i></span>
                </div>
            </div>
            <div class="nomessageselected" v-if="userMessage.messages.length == 0">
                <img :src="noMessageFound" alt="" style="display:inline-block;">
                <h3 class="no-message-found">No Message Found</h3>
            </div>
        </div>
        
        <div v-else class="nomessageselected">
            <img :src="noMessageSelected" alt="" style="display:inline-block;">
            <h3 class="no-message-selected">No conversation selected to display</h3>
        </div>
        <div class="message-footer" v-if="userMessage.user">
            <textarea class="form-control" id="custom-emoji" name="reply" placeholder="Type message here" @keydown="typeingEvent(userMessage.user.id)"  v-model="message" ></textarea>
            <a href="javascript:void(0);" @click="sendMessage" class="btnsendmsg"><i class="fa fa-telegram"></i></a>
        </div>
    </div>
</div>

        <!--/////Delete Modal-->
        <div id="id01" class="modal" v-if="userMessage.user">
  <div class="modal-content" >
    <div class="modal-container">
      <h2>{{deleteType == 'all' ?'Delete All Chat' : ''}}</h2>
      <p>{{deleteType == 'all' ? `Are you sure you want to delete all your chat with ${userMessage.user.name}` : (deleteMessageOwnerId == authuserr.id ? 'Delete Message' : `Delete message from  ${userMessage.user.name}`)}} ?</p>

      <div class="modal-clearfix">
        <button type="button" class="cancelbtn" @click.prevent="cancelDelete" :disabled="deletealldisable">Cancel</button>
        <a  class="deletebtn" @click.prevent="(deleteType == 'all') ? (deletealldisable ? '' : deleteAllMessage()) : (deleteSingleMessage(singleMessageId))" >{{deletealltext}}</a>
      </div>
    </div>
  </div>
</div>
    </div>
    
</template>


<script>
// Get the modal
var modal = document.getElementById('id01');

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}
import _ from 'lodash';
import { mapGetters } from "vuex";
import InfiniteLoading from "vue-infinite-loading";
import selecteduserloader from "./SelectedUserLoader.vue";
import sidebarr from "./Sidebarr.vue";
export default {
    components: {
        selecteduserloader,
        sidebarr
    },
    mounted(){
        this.authuserr=authuser;
        Echo.private(`chat.${authuser.id}`)
        .listen('MessageSend', (e) => {
            if(this.chat_start && this.userMessage.user.id == e.message.from){
                this.$store.dispatch('messageReceive',e);
                this.readedEvent(e.message.from);
                this.singleMessageId = e.message.id;
                axios.get('/message-readed/'+e.message.from)
                .then(response=>{
                })
            }
            this.$store.dispatch('userList');

        });
        this.$store.dispatch('userList');

        Echo.private('typingevent')
            .listenForWhisper('typing', (e) => {
                if(e.userId == authuser.id){
                    this.typing = {
                        name:e.user.name,
                        id:e.user.id
                    };
                }
                if(this.timerId){
                    return;
                }
                this.timerId=setTimeout(()=>{
                    this.typing = '';
                    this.timerId=undefined;
                },2000);
            });
        Echo.private('messagereaded')
        .listenForWhisper('readed', (e) => {
        if(e.userId == authuser.id){
            setTimeout(() => {
            jQuery('.unread_tick').css('color','#07bf07');
            }, 1000);
        }
        });
        Echo.join('liveuser')
            .here((users) => {
            this.users = users
            })
            .joining((user) => {
                this.users.push(user)
                this.onlineUser(user.id)
            })
            .leaving((user) => {
                this.users = this.users.filter(function (userr){
                    if(userr.id != user.id){
                        return userr;
                    }
                    return '';
                })
            });

        },
    data(){
        return{
            timerId:'',
            page: 1,
            message:'',
            typing:{} ,
            noMessageSelected:noMessageSelected,
            authuserr:{},
            noMessageFound:noMessageFound,
            users:[],
            online:'',
            chat_start:false,
            deleteType:'',
            singleMessageId:'',
            deleteMessageOwnerId:'',
            appName:appName,
            activeunreadcount:'',
            deletealldisable:false,
            deletealltext:'Delete',
            logout:logout,
            profileUpdate:profileUpdate,
            profileImagePath:profileImagePath,
            appUrl:appUrl
        }
    },
    computed:{
        userList(){
            return  this.$store.getters.userList;
        },
        userMessage(){
            return  this.$store.getters.userMessage;
        },
        ///this is for watcher userMessageUser which loads emojionearea/////
        userMessageUser(){
            var c = this.$store.getters.userMessage;
            return c.user
        },
    },
    created(){
        
    },
    watch:{
        ////with this watcher emojionearea function called due to which emojionearea is added in textarea////
        userMessageUser:{
            deep: true,
            handler: function() {
                this.cd();
            }
        }
    },
    methods:{
        /////for adding and configuring vue scolladdmore ///////
        infiniteHandler($state) {
            if(this.page == 1){
                //console.log('l')
                $state.complete();
                setTimeout(()=>{this.page++;$state.reset()},600)
            }else{
            this.$store.dispatch('userMessage',{userId:this.userMessage.user.id,page:this.page,chat_start:true})
            .then(response => {
                if(response == 'havemoredata'){
                    console.log($state)
                    $state.loaded();
                }else{
                    $state.complete();
                }
                }, error => {
                    //$state.error();
            })
            this.page++;
            }
            
        },
        ////for configuring and adding emojionearea in textarea//////
        cd(){
            setTimeout(()=>{
                jQuery("#custom-emoji").emojioneArea({
                    hidePickerOnBlur: true,
                    saveEmojisAs: 'unicode',
                    events: {
                        keydown: (editor, event)=> {
                            this.typeingEvent(this.userMessage.user.id);
                        }
                    }
                }); 
            },60)
        },
        ///when delete popup opens and cancel clicked/////
        cancelDelete(){
            document.getElementById('id01').style.display='none';
            this.deletealldisable = false;
            this.deletealltext='Delete';
        },
        activecount(activeunreadcoun){
            this.$store.dispatch('activeunreadcoun',activeunreadcoun)
            },
            ////on particular user click////
        async async(unread,id){
            jQuery('.loader-container').css('display','flex');
            ///this is for infintite scroll loader//
            this.page = 1;
            //////
            await this.activecount(unread); 
            await this.selectUser(id);
        },
        /////for mobile user sidebar opened bydefault/////
        openCoversations(){
            jQuery('#sidebar').toggleClass('opened');
        },
        
        selectUser(userId,chat_start){
            this.chat_start =true;
            ////for getting messages of selected userd
            this.$store.dispatch('userMessage',{userId:userId,chat_start:chat_start});
            ////
            this.$store.dispatch('userList');
            ////for making messages of selected user readed////
            this.readedEvent(userId);
            ////
            ///for mobile user sidebar closed after selecting user///
            jQuery('#sidebar').removeClass('opened');
            ///
            ///after user selected,unreaded messages are of different color to remove that color after some delay///
            setTimeout(function(){
                jQuery('.msg').removeClass('unread')
            },6000)
            /////
        //this.$store.dispatch('userSort',userId)
        },

        /////when send message button clicked/////
        sendMessage(e){
        e.preventDefault();
        var message = jQuery('#custom-emoji').val();
        if(message.trim() != ''){
            var random = Math.floor(Math.random());
            this.$store.dispatch('psuedoMessageAdd',{message:message,psuedoId:random});
            setTimeout(function(){
                var chatWindow = document.getElementById('message-wrap');
                var xH = chatWindow.scrollHeight;
                chatWindow.scrollTo(xH-1, xH);
            },20);
            axios.post('/senemessage',{message:message,user_id:this.userMessage.user.id})
            .then(response=>{
                this.singleMessageId = response.data.id - 1;
                this.$store.dispatch('messageAdd',{id:response.data.id - 1,read:response.data.read,psuedoId:random});
            })
            this.message = '';
            jQuery("#custom-emoji")[0].emojioneArea.setText('');
        }
        },

        ////when singlemessage delete button clicked this function is called for changing message id in popup///
        deleteSingleIdChange(messageId,ownerId){
            this.singleMessageId = messageId;
            this.deleteType='single';
            this.deleteMessageOwnerId = ownerId ;
            document.getElementById('id01').style.display='flex';
        },
        deleteSingleMessage(messageId){
            this.deletealldisable=true;
            this.deletealltext='Deleting...';
            axios.get(`/deletesinglemessage/${messageId}`)
            .then(response=>{
                this.$store.dispatch('psuedoMessageDelete',{messageId:messageId,userMessageId: jQuery('.list.active .latest-message').attr("data-id")});
                document.getElementById('id01').style.display='none';
                this.deletealldisable = false;
                this.deletealltext='Delete';
            })
            .catch(error=>{
                this.deletealldisable = false;
                this.deletealltext='Try again';
            })
        },
        ////when allmessage delete button clicked this function tells popup that this is for all message delete///
        allDeleteMessages(){
            document.getElementById('id01').style.display='flex';
            this.deleteType='all';
        },
        deleteAllMessage(){
            this.deletealldisable=true;
            this.deletealltext='Deleting...';
            axios.get(`/deleteallmessage/${this.userMessage.user.id}`)
        .then(response=>{
            this.$store.dispatch('psuedoMessageDelete',{messageId:'',userMessageId: jQuery('.list.active .latest-message').attr("data-id")});
            document.getElementById('id01').style.display='none'
            this.deletealldisable = false;
            this.deletealltext='Delete';
        })
        .catch(
            error=> {
                ///when any error occur in deleting message////
            this.deletealldisable = false;
            this.deletealltext='Try again';
            })
        },
        ///typing event triggers from cd()////
        typeingEvent(userId){
        Echo.private('typingevent')
        .whisper('typing', {
            'user': authuser,
            'typing':this.message,
            'userId':userId
        });
        },
        ///readed event triggers from sendmessage listening,selectUser()////
        readedEvent(userId){
        Echo.private('messagereaded')
        .whisper('readed', {
            'user': authuser,
            'userId':userId
        });
        },
        onlineUser(userId){
            return _.find(this.users,{'id':userId});
        }
    }
}


</script>