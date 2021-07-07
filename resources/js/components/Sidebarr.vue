<template>
    <sidebar class="opened" id="sidebar">
        <a :href="appUrl" class="logo-link"><span class="logo">{{appName}}</span></a>
            <div class="list-wrap" v-if="userList">
            <div class="list" @click.prevent="async(user.unread,user.id)" :data-count=user.unread v-for="user in userList" :key="user.id" :class="[userMessage.user ? ( userMessage.user.id===user.id ? 'active' : '') : '']">
            <a :href="profileImagePath+user.image" target="_blank"><img :src="profileImagePath+user.image" onerror="this.onerror=null;this.src=noPersonImage" alt="" /></a>
            <div class="info">
                <span class="user" v-if="user.name">{{user.name}}</span>
                <span class="text latest-message"  :data-id="user.latestMessageId" v-if="user.latestMessage">{{user.latestMessage}}</span>
                <span class="text" v-else>No conversation</span>
            </div>
            <span class="count" v-if="user.unread != 0">{{user.unread}}</span>
            <div v-if="!(typing && (typing.id == user.id))">
                <span class="time" v-if="onlineUser(user.id) || online.id == user.id">online</span>
                <span class="time" v-else>offline</span>
            </div>
            <div v-else>
                <span class="time">typing...</span>
            </div>
            </div>
            </div>
        </sidebar>
</template>
<script>
export default {
    props:['appUrl','appName','profileImagePath','online','typing'],
    data(){
        return{
           
        }
    },
    computed:{
        userList(){
            return  this.$store.getters.userList
        },
        userMessage(){
            return  this.$store.getters.userMessage
        }
    },
    methods: {
    onlineUser(userId){
        return this.$parent.onlineUser(userId);
    },
    async(userunread,userid){
        this.$parent.async(userunread,userid);
    }
    }
}
</script>