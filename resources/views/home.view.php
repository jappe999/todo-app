@extend('layout/base.view.php')

@section('body')
    <div id="app">
        <header>
            <navbar :user="user"></navbar>
        </header>
        <main>
            <div class="todo_items_wrapper">
                <h1>Hello {{ user.name }}</h1>
                <todoitem id="new_todo">
                    <input type="text" placeholder="To do..." v-model="new_item.title" @keypress.enter="add_todo">
                    <button type="button" class="button" @click.prevent="add_todo">
                        Add
                    </button>
                </todoitem>
                <div class="todo_items" v-for="item in items">
                    <todoitem :item="item" @edit="open_popup" @set_done="update_todo"></todoitem>
                </div>
            </div>
        </main>
        <popup :item="selected_item" :files="files" :is_open="popup_open" @close_popup="close_popup"></popup>
        <footer>
            <div id="footer_inner">
                Made with <a href="//youtu.be/lXMskKTw3Bc" target="_blank">&hearts;</a> by <a href="http://thenerdin.me" target="_blank">thenerdin.me</a>
            </div>
        </footer>
    </div>
    <script src="/js/app.js" charset="utf-8"></script>
@endsection
