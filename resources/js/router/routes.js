
/** 
 * * SHEESH 
 * ! sheesh
 * ? TF
 * TODO:sheesh
 */
const routes = [
    {
        path: "/:pathMatch(.*)*",
        name: "NotFound",
        component: () => import("@/views/Error/NotFound.vue"),
    },
    {
        path: "/",
            children: [
            {
              path: '',
              name: "home",
              component: () => import("@/views/Home/Index.vue")
            },
          ],
    },
    {
        path: "/login",
        name: "login",
        component: () => import("@/views/Authentication/Login.vue"),
    },
    {
        path: "/dashboard",
        name: "dashboard",
        component: () => import("@/views/Dashboard/Index.vue")
    },
    {
        path: "/registration",
        name: "registration-index",
        component: () => import("@/views/Registration/Index.vue"),
    },
    {
        path: "/product",
        children: [
            {
                path: '',
                name: "product-index",
                component: () => import("@/views/Product/Index.vue"),

            },
            {
                path: ':slug',
                name: "product-details",
                component: () => import("@/views/Product/Details.vue")
            },
          ],
    },
    {
        path: "/shop",
        children: [
            {
              path: '',
              name: "shop-index",
              component: () => import("@/views/Shop/Index.vue")
            },
            {
                path: ':slug',
                name: "shop-details",
                component: () => import("@/views/Shop/Details.vue")
            },
          ],
    },
    {
        path: "/cart",
        name: "cart-index",
        component: () => import("@/views/User/Cart.vue")
    },
    {
        path: "/typescript",
        name: "typescript",
        component: () => import("@/views/TsExample.vue")
    },
    {
        path: "/swiper",
        name: "swiper",
        component: () => import("@/components/Carousel/SlideVertical.vue")
    },
    {
        path: "/roles",
        name: "roles",
        component: () => import("@/views/Admin/Role/Index.vue")
    },
    {
        path: "/permissions",
        name: "permissions",
        component: () => import("@/views/Admin/Permission/Index.vue")
    },
    {
        path: "/menus",
        name: "menus",
        component: () => import("@/views/Admin/Menu/Index.vue")
    },
    {
        path: "/users",
        name: "users",
        component: () => import("@/views/Admin/User/Index.vue")
    },
   

    
 
];

export default routes;
