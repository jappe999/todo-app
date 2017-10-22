<template>
    <div class="popup_wrapper" :class="{ 'active': is_open }">
        <div class="popup_background" @click="close_popup"></div>
        <div class="popup" :class="{ 'active': is_open }" @click="close_all" id="popup">
            <div class="popup__close" @click="close_popup" title="Close">
                <i class="fa fa-times fa-2x"></i>
            </div>
            <div class="popup__title" @click="edit_title" id="edit_title">
                <h2 v-if="!this.title_edit">
                    {{ item.title }}
                </h2>
                <input v-else type="text" :value="item.title" v-model="item.title"
                       @keypress.enter="update_item" />
            </div>
            <div v-if="!this.description_edit" @click="edit_description" class="popup__description">
                <span v-if="this.item.description">
                    {{ item.description }}
                </span>
                <span v-else>
                    No description
                </span>
            </div>
            <div v-else class="popup__description">
                <textarea>{{ item.description }}</textarea>
                <button type="button" @click="update_item">Update</button>
            </div>
            <div class="popup__assignee">
                <span v-if="item.assignee">
                    {{ item.assignee.name }}
                </span>
                <span v-else>
                    Choose a user
                </span>
            </div>

            <!-- Files -->
            <div id="files" for="file in files">
                <div class="file">

                </div>
            </div>

            <button class="popup__item_delete" type="button" title="Delete todo item">Delete</button>
        </div>
    </div>
</template>

<script src="../js/popup.js" charset="utf-8"></script>
<style lang="scss" src="../sass/popup.scss"></style>
