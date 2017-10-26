<template>
    <div class="popup_wrapper" :class="{ 'active': is_open }">
        <div class="popup_background" @click="close_popup"></div>
        <div class="popup" :class="{ 'active': is_open }" @click="close_all" id="popup">
            <div class="popup__close" @click="close_popup" title="Close">
                <i class="fa fa-times fa-2x"></i>
            </div>
            <div class="popup__title" @click="edit_title" id="edit_title" title="Edit title">
                <h2 v-if="!title_edit">
                    {{ item.title }}
                </h2>
                <input v-else type="text" :value="item.title" v-model="item.title"
                       @keypress.enter="update_item" />
            </div>
            <main class="popup__content">
                <div class="margin-top-10">
                  <b>Description:</b>
                </div>
                <div v-if="!description_edit" @click="edit_description" class="popup__description" title="Edit description">
                    <span v-if="item.description">
                        {{ item.description }}
                    </span>
                    <span v-else>
                        No description
                    </span>
                </div>
                <div v-else class="popup__description">
                    <textarea v-model="item.description">{{ item.description }}</textarea>
                    <button type="button" @click="update_close_all">Update</button>
                </div>
                <div class="popup__assignee">
                  <b>Assignee:</b>
                  <span v-if="!assignee_edit" @click="edit_assignee">
                      <span v-if="item.assignee">
                          {{ item.assignee.name }}
                      </span>
                      <span v-else>
                          Choose a user
                      </span>
                  </span>
                  <select v-else v-model="item.assignee" @change="update_close_all">
                      <option :value="user" v-for="user in users" :key="user.key" :selected="user_is_assigned">
                          {{ user.name }}
                      </option>
                  </select>
                </div>

                <!-- Files -->
                <div class="popup__files">
                    <b>Files</b>
                    <file :file="file" v-for="file in files" :key="file.id" @remove_file="remove_file"></file>
                    <div class="popup__files__upload">
                        <input class="popup__files__upload_input" ref="file_upload" type="file" @change="set_files" multiple>
                        <div class="popup__files__upload_area" @click="select_files">
                            <span>
                                <i class="fa fa-file">&nbsp;</i>
                                {{ files_to_upload.join(', ') }}
                            </span>
                            <span v-if="files_to_upload.length < 1">Click here to select files</span>
                        </div>
                        <button type="button" class="popup__files__upload_button" @click="upload_files">Upload</button>
                    </div>
                </div>

                <button class="popup__item_delete" type="button" title="Delete todo item">
                    Delete task
                </button>
            </main>
        </div>
    </div>
</template>

<script src="../js/popup.js" charset="utf-8"></script>
<style lang="scss" src="../sass/popup.scss"></style>
