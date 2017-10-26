<template>
    <div class="todo_item">
        <slot>
            <div v-if="item">
                <span class="todo_item__title">
                    {{ item.title }}
                </span>
                <div class="todo_item__assignee" v-if="item.assignee">
                    <i>Assigned to: {{ item.assignee.name }}</i>
                </div>
            </div>
            <button type="button" title="Edit" class="todo_item__edit" @click.prevent="edit">
                <i class="fa fa-cog"></i>
            </button>
            <button type="button" title="Archive" class="todo_item__check" @click.prevent="set_done" v-if="item && item.is_done === '0'">
                <i class="fa fa-check"></i>
            </button>
            <button type="button" title="Revert" class="todo_item__revert" @click.prevent="set_todo" v-if="item && item.is_done === '1'">
                <i class="fa fa-undo"></i>
            </button>
        </slot>
    </div>
</template>

<script src="../js/todo_item.js" charset="utf-8"></script>
<style lang="scss" src="../sass/todo_item.scss"></style>
