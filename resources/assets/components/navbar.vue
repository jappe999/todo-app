<template>
    <nav id="navbar">
        <!-- <div id="hamburger" class="pull-right" @click="toggle_menu"></div> -->
        <ul>
            <li>

            </li>
        </ul>
        <div class="pull-right">
            <ul>
                <li>
                    Hello {{ user['name'] }}
                </li>
                <li>
                    <a href="/logout">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
</template>

<script src="../js/navbar.js" charset="utf-8"></script>
<style lang="scss" src="../sass/navbar.scss"></style>
