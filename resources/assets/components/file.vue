<template>
    <div class="file">
        <a :href="'/api/files/get/' + file.id" class="file__name">
            {{ file.name }}
        </a>
        <button role="button" @click="delete_file(file)" class="file__delete">
            Delete file
        </button>
    </div>
</template>
<script src="../js/file.js"></script>
<style src="../sass/file.scss" lang="scss" scoped></style>
