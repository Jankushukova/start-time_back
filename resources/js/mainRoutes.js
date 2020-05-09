import VueRouter from 'vue-router';

import Home from "./components/Home";
import Create from "./components/Create";
import ManagerRequests from "./components/ManagerRequests";
import Answer from "./components/Answer";
window.Vue = require('vue');

window.Vue.use(VueRouter);


var router =  new VueRouter({
    routes: [

        {
            path: '/requests',
            component: Home,
            name: 'requests',

        },
        {
            path: '/requests/create',
            component: Create,
            name: 'create',

        },
        {
            path: '/manager/requests',
            component: ManagerRequests,
            name: 'managerRequests',

        },
        {
            path: '/manager/requests/answer:message_id',
            component: Answer,
            name: 'managerAnswer',

        },

    ]
});
export default router
