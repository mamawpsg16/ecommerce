<template>
    <Modal class="modal-lg" targetModal="create-role-modal" modaltitle="Add role" :backdrop="true" :escKey="false">
        <template #body>
            <form @submit.prevent="storeConfirmation" class="mt-1" id="create-role-form">
                <div class="row mb-3">
                    <div class="col-12">
                        <label>Name <span class="text-danger">*</span></label>
                        <Input type="text" v-model="role.name"
                            :class="{ inputInvalidClass: checkInputValidity('role', 'name', ['required']) }" required
                            autocomplete="role_name" />
                        <div v-if="v$.role.name.$dirty"
                            :class="{ 'text-danger': checkInputValidity('role', 'name', ['required']) }">
                            <p v-if="v$.role.name.required.$invalid">
                                Name is required.
                            </p>
                        </div>
                        <div v-if="errors?.name" class="text-danger">
                            {{ errors?.name[0] }}
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" :class="{ 'border-danger': taggedPermissionsCount <= 0 }">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th>Permissions</th>
                                <th class="text-center" style="font-size:20px"><input type="checkbox"
                                        class="form-check-input" :checked="selectAll" @change="tagAllPermissions"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(menu, index) in menus" :key="menu.value">
                                <td>{{ menu.label }}</td>
                                <td v-if="role.permissions[index]?.permissions_array">
                                    <VueMultiselect :loading="loadingPermissions" :disabled="loadingPermissions" :multiple="true" track-by="label" label="label" v-model="role.permissions[index].permissions_array" :taggable="true" placeholder="Select permissions" :options="permissions"></VueMultiselect>
                                </td>
                                <td class="text-center" style="font-size:15px"
                                    v-if="role.permissions[index]?.checked === true || role.permissions[index]?.checked === false">
                                    <input type="checkbox" class="form-check-input" :checked="role.permissions[index].checked" @change="selectAllMenuPermissions(index)"></td>
                            </tr>
                            <p v-if="taggedPermissionsCount <= 0" class="text-danger">Permission Required</p>
                        </tbody>
                    </table>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-md btn-secondary me-1 px-3" @click="resetForm" alt="reset"><i class="fa-solid fa-rotate-left"></i></button>
                    <button type="submit" class="btn btn-primary btn btn-md btn-primary me-1 px-5">Save</button>
                </div>
            </form>
        </template>
    </Modal>
</template>

<script>
import Modal from '@/components/Modal/modal.vue';
// import FloatingInput from '@/components/Form/FloatingInput.vue'
import Input from '@/components/Form/Input.vue'
import { useVuelidate } from '@vuelidate/core'
import { required } from '@vuelidate/validators';
import { swalConfirmation, swalSuccess, swalError, SwalDefault } from '@/helpers/Notification/sweetAlert.js';
import { checkValidity } from '@/helpers/Vuelidate/InputValidation.js';
import { generateUniqueSlug } from '@/helpers/PartialHelpers/index.js';
import axios from 'axios';
import VueMultiselect from 'vue-multiselect'
export default {
    name: 'Role Create',
    setup() {
        return { v$: useVuelidate({ $autoDirty: true }) }
    },
    emits: ['loadUpdatedRoles'],
    data() {
        return {
            role: {
                name: null,
                permissions: [],
            },
            selectAll: true,
            isSaving: false,
            auth_token: `Bearer ${localStorage.getItem('auth-token')}`,
            errors: [{
                name: false,
            }],
            permissions: [],
            menus: [],
            loadingMenus: false,
            loadingPermissions: false,
            taggedPermissionsCount: 0
        }
    },
    validations() {
        return {
            role: {
                name: { required },
            },
        }
    },
    components: {
        Modal,
        Input,
        VueMultiselect
    },
 
    async created() {
        await this.getMenus();
        await this.getPermissions();
    },
    methods: {
        async getPermissions() {
            this.loadingPermissions = true;
            await axios.get('/api/admin/get-permissions', {
                headers: {
                    Authorization: this.auth_token
                }
            })
                .then((response) => {
                    const { permissions } = response.data;

                    const permissions_array = permissions;
                    this.permissions = permissions;

                    this.menus.forEach(menu => {
                        this.role.permissions.push({
                            menu_id: menu.value,
                            checked: true,
                            permissions_array: [...permissions_array],
                        });
                    });

                    this.loadingPermissions = false;
                })
                .catch((error) => {
                    console.log(error, 'error');
                });
        },

        async getMenus() {
            this.loadingMenus = true;
            await axios.get('/api/admin/get-menus', {
                headers: {
                    Authorization: this.auth_token
                }
            })
                .then((response) => {
                    const { menus } = response.data;

                    this.taggedPermissionsCount = menus.length

                    this.menus = menus;

                    this.loadingMenus = false;
                })
                .catch((error) => {
                    console.log(error, 'error');
                });
        },

        tagAllPermissions() {

            this.selectAll = !this.selectAll;

            this.role.permissions = this.menus.map(menu => ({
                menu_id: menu.value,
                permissions_array: this.selectAll ? [...this.permissions] : [],
                checked: this.selectAll
            }));

            this.taggedPermissionsCount = this.role.permissions.reduce((prev, cur) => prev + (cur.permissions_array.length >= 1 ? 1 : 0), 0);
        },

        selectAllMenuPermissions(index) {
            const checkbox = this.role.permissions[index].checked = !this.role.permissions[index].checked;
            this.role.permissions[index].permissions_array = checkbox ? [...this.permissions] : [];

            this.taggedPermissionsCount = this.role.permissions.reduce((prev, cur) => prev + (cur.permissions_array.length >= 1 ? 1 : 0), 0);

            this.selectAll = this.taggedPermissionsCount == this.menus.length ? true : false;
        },

        checkInputValidity(parentProperty = null, dataProperty, validations = []) {
            return checkValidity(this.v$, parentProperty, dataProperty, validations);
        },

        resetForm() {

            this.role = {
                name: null,
                permissions: this.menus.map(menu => {
                    return {
                        menu_id: menu.value,
                        permissions_array: [...this.permissions],
                        checked: true
                    }
                })
            }

            this.selectAll = true;
            this.taggedPermissionsCount = this.menus.length;


            this.errors = [];
            document.querySelector('#create-role-form').reset();
            this.v$.$reset();

        },

        async store() {
            this.isSaving = true;
            let menu_ids = [];
            const result = this.role.permissions.reduce((result, element) => {
                if (element.permissions_array.length >= 1) {
                    menu_ids.push(element.menu_id);
                    result[element.menu_id] = element.permissions_array.map(permission => permission.value);
                }
                return result;
            }, {});

            // const menu_permissions = {};
            // menu_ids.forEach((menu_id,) => {
            //     menu_permissions[menu_id] = result[menu_id]
            // });
            // console.log(menu_permissions,'menu_permissions');
//             [
//     1 => ['expires' => true],
//     2 => ['expires' => true],
// ]

            const role = {
                ...this.role,
                menu_ids: menu_ids,
                menu_permissions: result,
                slug: generateUniqueSlug(this.role.name)
            };

            axios.post('/api/admin/roles', role, {
                headers: {
                    Authorization: this.auth_token,
                }
            })
                .then((response) => {
                    this.isSaving = false;
                    if (response.data.errors) {
                        this.errors = response.data.errors;
                        SwalDefault.close();
                    } else {
                        console.log('SOKPA')
                        this.resetForm();

                        this.$emit('loadUpdatedRoles');
                        SwalDefault.fire({
                            icon: "success",
                            text: "Role Successfully Saved.",
                            showConfirmButton: false,
                        });
                    }
                })
                .catch((error) => {
                    console.log(error, 'error');
                    this.isSaving = false;
                    // if(error.response.status == 422){
                    this.errors = error.response.data.errors;
                    SwalDefault.close();
                    // }
                });
        },

        async storeConfirmation() {
            if (!await this.v$.$validate() || this.taggedPermissionsCount <= 0) {
                return;
            }
            swalConfirmation().then((result) => {
                if (result.isConfirmed) {
                    this.store()
                }
            });
        },
    },

    watch: {
        isSaving(newValue, oldValue) {
            if (newValue) {
                SwalDefault.fire({
                    title: '<i class="fa fa-cog fa-spin"></i>&nbsp;Saving...',
                    text: "Saving changes, kindly wait.",
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                });
            }
        }
    },
}
</script>

<style scoped>.inputInvalidClass {
    border: 1px solid red;
    /* Adjust the style as needed */
    border-radius: 5px;
}</style>