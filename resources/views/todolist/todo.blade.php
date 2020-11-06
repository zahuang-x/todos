<!doctype html>
<html lang="zh-Cn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{'/home/css/bootstrap.min.css'}}">
    <link rel="stylesheet" href="{{'/home/css/base.css'}}">
    <link rel="stylesheet" href="{{'/home/css/index.css'}}">
    <link rel="stylesheet" href="{{'/home/css/nprogress.css'}}">
    <title>todolist</title>
</head>
<body>
<section class="todoapp">
    <header class="header">
        <h1>todos</h1>
        <input type="text" class="new-todo" placeholder="What needs to be done?" autofocus id="task">
    </header>
    <!-- This section should be hidden by default and shown when there are todos -->
    <section class="main">
        <ul class="todo-list" id="todo-list">

            @foreach($data as $key => $v)
                <li class="{{$v->status == 1 ? 'completed':''}}">
                    <div class="view">
                        <span class="glyphicon {{$v->status == 1?  'glyphicon-check' : 'glyphicon-unchecked'}} toggle"></span>
                        <label>{{$v->content}}</label>
                        <button class="destroy" data-id="{{$v->id}}"></button>
                    </div>
                    <input class="edit">
                </li>
            @endforeach

        </ul>
    </section>
    <!-- This footer should hidden by default and shown when there are todos -->
    <footer class="footer">
        <!-- This should be `0 items left` by default -->
        <span class="todo-count"><strong id="count">{{count($data)}}</strong> item left</span>
        <!-- Remove this if you don't implement routing -->
        <ul class="filters">
            <li>
                <a class="selected" href="javascript:;" id="all">All</a>
            </li>
            <li>
                <a href="javascript:;" id="active">Active</a>
            </li>
            <li>
                <a href="javascript:;" id="completed">Completed</a>
            </li>
        </ul>
        <!-- Hidden if no completed items are left ↓ -->
        <button class="clear-completed">Clear completed</button>
    </footer>
</section>

<script src="{{'/home/js/jquery3.2.1.js'}}"></script>
<script>
    $(function () {
        // 用于存放任务列表的数组
        var taskAry = [];
        // 选择任务列表容器
        var taskBox = $('#todo-list');
        // 添加任务的文本框
        var taskInp = $('#task');

        var strong = $('#count');

        // 获取文本框并且添加键盘抬起事件  添加任务
        taskInp.on('keyup', function (event) {
            // 如果用户敲击的是回车键
            if (event.keyCode == 13) {
                // 判断用户是否在文本框中输入了任务名称
                var taskName = $(this).val();
                // // 如果用户没有在文本框中输入内容
                if (taskName.trim().length == 0) {
                    alert('请输入任务名称')
                    // 阻止代码向下执行
                    return;
                }
                // 向服务器端发送请求 添加任务
                $.ajax({
                    type: 'post',
                    url: "{{route('todolist.create')}}",
                    data: {
                        'content': taskName,
                        '_token': "{{csrf_token()}}"
                    },
                    success: function (response) {
                        // console.log(response);

                        if (response.code == 200) {
                            let id = response.data.id;
                            let content = response.data.content;
                            //向表格中添加数据
                            let data = `<li>
                    <div class="view">
                        <span class="glyphicon glyphicon-unchecked toggle"></span>
                        <label>${content}</label>
                        <button class="destroy" data-id="${id}"></button>
                    </div>
                    <input class="edit">
                </li>`;
                            taskBox.prepend(data);
                            // 添加完成之后清空文本框中的内容
                            taskInp.val('');
                            //获取任务数量并添加到元素中
                            let count = taskBox.children('li').length;
                            strong.text(count);
                        }

                    }
                })
            }
        });

        //删除任务
        taskBox.on('click', '.destroy', function () {
            // 要删除的任务的id
            var id = $(this).attr('data-id');
            var that = $(this);
            // 向服务器端发送请求删除 任务
            $.ajax({
                url: "{{route('todolist.destroy')}}",
                type: 'POST',
                data: {
                    '_method':'DELETE',
                    id,
                    '_token':"{{csrf_token()}}"
                },
                success: function (response) {
                    if(response.code == 200){
                        that.parents('li').remove();
                        //获取任务数量并添加到元素中
                        let count = taskBox.children('li').length;
                        strong.text(count);
                    }
                }
            })
        });

        //点击label标签  完成任务
        taskBox.on('click','.glyphicon',function(){
            //获取label标签元素的下一个相邻元素 button
            var el = $(this).next().next();
            //获取id
            var id = el.attr('data-id');
            var completed = el.parents('li').hasClass('completed');
            var span = el.prev().prev();

            $.ajax({
                url:"{{route('todolist.change')}}",
                type:'POST',
                data:{
                    id,
                    '_token':"{{csrf_token()}}",
                },
                success:function(res){
                    if(res.code == 200){
                        if(completed){
                            el.parents('li').removeClass('completed');
                            span.removeClass('glyphicon-check');
                            span.addClass('glyphicon-unchecked');

                        }else{
                            el.parents('li').addClass('completed');
                            span.removeClass('glyphicon-unchecked');
                            span.addClass('glyphicon-check');
                        }

                    }
                }
            });

        });

        //删除全部完成的任务
        $('.clear-completed').on('click',function(env){
            var lis = $('#todo-list').children('li.completed');
            var ids = [];
            console.log(lis);
            for(let i=0;i<lis.length;i++){
                let id = $(lis[i]).find('.destroy').attr('data-id');
                ids.push(id);
            }
            $.ajax({
               url:"{{route('todolist.destroy')}}",
                type:"POST",
                data:{
                   id:ids,
                    '_method':"DELETE",
                    '_token':"{{csrf_token()}}"
                },
                success:function (res) {
                    if(res.code == 200){
                        $('#todo-list').children('li.completed').remove();
                    }
                }
            });
        });

        $('#active').on('click',function(env){
            $('.filters').find('a').removeClass();
            $(this).addClass('selected');
            $.ajax({
                url:"{{route('todolist.status')}}",
                type:"GET",
                data:{
                    status:0,
                    _token:"{{csrf_token()}}"
                },
                success:function (res) {
                    let html = ``;
                    for(let i = 0; i < res.length; i++){
                        html += `<li>
                    <div class="view">
                        <span class="glyphicon glyphicon-unchecked toggle"></span>
                        <label>${res[i].content}</label>
                        <button class="destroy" data-id="${res[i].id}"></button>
                    </div>
                    <input class="edit">
                </li>`;

                    }
                    taskBox.html(html);
                }
            });
        });

        $('#completed').on('click',function(env){
            $('.filters').find('a').removeClass();
            $(this).addClass('selected');
            $.ajax({
                url:"{{route('todolist.status')}}",
                type:"GET",
                data:{
                    status:1,
                    _token:"{{csrf_token()}}"
                },
                success:function (res) {
                    let html = ``;
                    for(let i = 0; i < res.length; i++){
                        html += `<li class="completed">
                    <div class="view">
                        <span class="glyphicon glyphicon-check toggle"></span>
                        <label>${res[i].content}</label>
                        <button class="destroy" data-id="${res[i].id}"></button>
                    </div>
                    <input class="edit">
                </li>`;

                    }
                    taskBox.html(html);
                }
            });
        });

        $('#all').on('click',function(env){
            location.reload()
        });
    })
</script>
</body>
</html>
