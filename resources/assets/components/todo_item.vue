<template>
    <div class="todo_item">
        <slot>
            <div>
                <span class="todo_item__title" v-if="item">
                    {{ item.title }}
                </span>
                <div class="todo_item__assignee" v-if="item">
                    {{ item.assignee }}
                </div>
            </div>
            <button type="button" title="Aanpassen" class="todo_item__edit" @click.prevent="edit">
                <i class="fa fa-cog"></i>
            </button>
            <button type="button" title="Afvinken" class="todo_item__check" @click.prevent="set_done">
                <i class="fa fa-check"></i>
            </button>
        </slot>
    </div>
</template>

<script src="../js/todo_item.js" charset="utf-8"></script>
<style lang="scss" src="../sass/todo_item.scss"></style>
