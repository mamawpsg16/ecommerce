<template>
    <Modal class="modal-lg" targetModal="role-details-modal" :modaltitle="role.name" :backdrop="true" :escKey="false">
        <template #body>
            <form @submit.prevent="updateConfirmation" class="mt-1" id="role-details-form">
                <div class="row mb-3">
                    <div class="col-12">
                        <label>Name <span class="text-danger">*</span></label>
                        <Input type="text" v-model="role.name" :class="{ inputInvalidClass: checkInputValidity('role', 'name', ['required']) }" required autocomplete="role_name" />
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
                                    <VueMultiselect :loading="loadingPermissions" :disabled="loadingPermissions"
                                        :multiple="true" track-by="label" label="label"
                                        v-model="role.permissions[index].permissions_array" :taggable="true"
                                        placeholder="Select permissions" :options="permissions"></VueMultiselect>
                                </td>
                                <td class="text-center" style="font-size:15px"
                                    v-if="role.permissions[index]?.checked === true || role.permissions[index]?.checked === false">
                                    <input type="checkbox" class="form-check-input"
                                        :checked="role.permissions[index].checked"
                                        @change="selectAllMenuPermissions(index)">
                                </td>
                            </tr>
                            <p v-if="taggedPermissionsCount <= 0" class="text-danger">Permission Required</p>
                        </tbody>
                    </table>
                </div>

                <div class="text-end">
                    <button type="button" class="btn btn-md btn-secondary me-1 px-3" @click="closeModal">Close</button>
                    <button type="submit" class="btn btn-md btn-primary me-1 px-3">Update</button>
                </div>
            </form>
        </template>
    </Modal>
</template>

<script>
import Modal from '@/components/Modal/modal.vue';
import Input from '@/components/Form/Input.vue'
import { useVuelidate } from '@vuelidate/core'
import { required } from '@vuelidate/validators';
import { deepClone } from '@/helpers/PartialHelpers/index.js';
import { checkValidity } from '@/helpers/Vuelidate/InputValidation.js';
import { swalConfirmation, SwalDefault } from '@/helpers/Notification/sweetAlert.js';
import VueMultiselect from 'vue-multiselect'
export default {
    name: 'Product Details',
    props: {
        role_details: [Object, Array],
        menu_permissions: [Object, Array],
        role_id: [Number],
        index: [Number]
    },
    emits: ['updateRole'],
    setup() {
        return { v$: useVuelidate({ $autoDirty: true }) }
    },
    data() {
        return {
            role: {
                name: null,
                permissions: [],
            },
            selectAll: false,
            permissions: [],
            menus: [],
            loadingMenus: false,
            loadingPermissions: false,
            taggedPermissionsCount: 0,
            auth_token: `Bearer ${localStorage.getItem('auth-token')}`,
            edit: false,
            isUpdating: false,
            prevDetails: null,
            errors: [{
                name: false,
            }],
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
        resetForm() {
            // const form = document.querySelector('#role-details-form');
            // if (form) {
            //     form.reset();
            // }
            this.errors = [];

            this.v$.$reset();
        },

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

                    this.permissions = permissions_array;

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

                    // this.taggedPermissionsCount = menus.length

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

        editDetails() {
            this.edit = true;
        },

        cancelEdit() {
            this.edit = false;
            this.resetForm();
        },


        checkInputValidity(parentProperty = null, dataProperty, validations = []) {
            return checkValidity(this.v$, parentProperty, dataProperty, validations);
        },


        closeModal() {
            const id = document.getElementById('role-details-modal');
            const modal = bootstrap.Modal.getOrCreateInstance(id);
            this.resetForm();
            modal.hide();
        },

        async update() {
            this.isUpdating = true;
            let ids = [];
            const permission_details = this.role.permissions.reduce((result, element) => {
                if (element.permissions_array.length) {
                    ids.push(element.id);
                    result[element.menu_id] = element.permissions_array.map(permission => permission.value);
                }
                return result;
            }, {});

            const role = {
                name:this.role.name,
                menu_ids: ids,
                id: this.role.id,
                permissions: permission_details,
                menu_permission_ids: this.role_details.menu_permission_ids
            };

            console.log(role,'role');
            axios.put(`/api/admin/roles/${this.role_id}`, role, {
                headers: {
                    Authorization: this.auth_token,
                }
            })
                .then((response) => {
                    const { data, menu_ids, menu_permissions } = response.data;

                    this.isUpdating = false;
                    if (response.data?.errors !== undefined) {
                        this.errors = response.data.errors;
                        SwalDefault.close();
                    } else {

                        this.edit = false;
                        data.menu_permissions = menu_permissions;
                        data.menu_ids = menu_ids;
                        this.role.name = data.name;

                        this.$emit('updateRole', this.index, data);

                        SwalDefault.fire({
                            icon: "success",
                            text: "Succesfully updated!",
                            showConfirmButton: false,
                            timer: 1500
                        });

                        this.resetForm();
                    }

                })
                .catch((error) => {
                    this.isUpdating = false;
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                        SwalDefault.close();
                    }
                });
        },


        async updateConfirmation() {
            if (!await this.v$.$validate() || this.taggedPermissionsCount <= 0) {
                return;
            }
            swalConfirmation().then((result) => {
                if (result.isConfirmed) {
                    this.update()
                }
            });
        },
    },

    mounted() {
    },

    watch: {
        role_details() {
            this.edit = false;
            this.prevDetails = deepClone(this.role_details);
            this.role = this.role_details;
            this.role.name = this.role_details.name;

            // this.formatPermissions(this.role_details.menu_permissions, this.role_details.menu_ids)
            let permission_arr = [];
            let processedMenuIds = new Set();
            // this.selectAll = this.role_details.menu_permissions.length == this.menus.length ? true : false;
            this.taggedPermissionsCount = this.role_details.menu_permissions.length;
            this.menus.forEach(menu => {
                if (this.role_details.menu_ids.includes(menu.value)) {
                    this.role_details.menu_permissions.forEach(details => {
                        console.log(details,'details');
                        const permissions = details.permissions.split(',');
                        console.log(permissions, 'permissions', permissions.includes(`${menu.value}`), `${menu.value}`);
                        if (permissions.includes(`${menu.value}`) && !processedMenuIds.has(menu.value)) {
                            const permissionsArray = permissions.map(permissionValue => {
                                const matchingPermission = this.permissions.find(permission => permission.value == permissionValue);
                                if (matchingPermission) {
                                    return {
                                        label: matchingPermission.label,
                                        value: permissionValue
                                    };
                                }
                            });
                            permission_arr.push({
                                menu_id: menu.value,
                                id: details.id,
                                checked: false,
                                permissions_array: permissionsArray
                            });

                            processedMenuIds.add(menu.value);
                        }
                    });
                } else {
                    permission_arr.push({
                        menu_id: menu.value,
                        id: details.id,
                        checked: false,
                        permissions_array: []
                    });
                }

            });
            console.log(permission_arr,'permission_arr')
            this.role.permissions = permission_arr;

            immediate: true
        },

        isUpdating(newValue, oldValue) {
            if (newValue) {
                SwalDefault.fire({
                    title: '<i class="fa fa-cog fa-spin"></i>&nbsp;Updating...',
                    text: "Saving changes, kindly wait.",
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                });
            }
        }
    }
}
</script>

<style scoped>
.inputInvalidClass {
    border: 1px solid red;
    /* Adjust the style as needed */
    border-radius: 5px;
}</style>