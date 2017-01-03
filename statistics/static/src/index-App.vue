<template>
    <div id="app">
        <div id="main">
            <div id="content">
                <router-view></router-view>
            </div>
        </div>
    </div>
</template>
<script>
    import Index from './Index.vue'
    import Tread from './tread.vue'
    import Visit from './visit.vue'
    import Exception from './exception.vue'
    import Exception2 from './exception2.vue'
    export default{
        data () {
            return {
            }
        },
        methods:{
        },
        components: {
            Index,
            Tread,
            Visit
        },
        routes: [
            { path: '/', component: Index },
            { path: '/tread', component: Tread },
            { path: '/preview', component: Index },
            {path: '/visit',component:Visit},
            {path: '/exception',component:Exception},
            {path: '/exception2',component:Exception2}
        ]
    }
</script>
<style>

</style>