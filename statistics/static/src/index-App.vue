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

    export default{
        data () {
            return {
            }
        },
        methods:{
        },
        components: {
            Index,
            Tread
        },
        routes: [
            { path: '/', component: Index },
            { path: '/tread', component: Tread },
            { path: '/preview', component: Index }
        ]
    }
</script>
<style>

</style>