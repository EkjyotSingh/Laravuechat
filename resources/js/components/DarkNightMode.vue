<template>
    <div :class="[darkmodelaravuechat == 'on' ? 'modeactive' : '','checkbox']">
        <div class="inner">
            <div class="toggle" @click="modeChange"></div>
        </div>
    </div>
</template>
<script>
export default{
    data(){
        return{
            darkmodelaravuechat:localStorage.getItem('darkmodelaravuechat'),
        }
    },
    methods:{
        modeChange(){
            $('.checkbox').toggleClass('modeactive');
                if($('body').hasClass('dark')){
                    localStorage.setItem('darkmodelaravuechat', 'off')
                    $('body').removeClass('dark');
                }else{
                    localStorage.setItem('darkmodelaravuechat', 'on')
                    $('body').addClass('dark');
                }
        }
    }
}
</script>