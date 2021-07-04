window.Vue = require('vue').default;

window.moment = require('moment');
Vue.filter('timeformat',function(arg){
 return moment(arg).format('MMMM Do YYYY, h:mm:ss a')
});