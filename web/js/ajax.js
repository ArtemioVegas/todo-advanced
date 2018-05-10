function deleteOneTask(id, $url){
    $.ajax({
        type: "POST",
        url:  $url,
        async: "true",
        dataType: "json",
        success: function (data, status, object) {
            if (!data.error) {

                $("tr#"+id+".tasks__item").remove();

                // пересчитываем задачи
                recalcTasksInPorjects();
            }
            // Добавляем сообщение
            addFlash(data.message,data.error);
        },
        error: function (data, status, object) {
            addFlash('При ajax запросе произошла ошибка. Код: ' + data.status + ' Содержание: '+ object);
        }
    });
}

function completeOneTask(id,$url){
    $.ajax({
        type: "POST",
        url:  $url,
        async: "true",
        dataType: "json",
        success: function (data, status, object) {

            if (!data.error) {

                msgType = true;

                if(getParameterByName('show_completed') === '1'){
                    $("tr#"+id+".tasks__item").removeClass( "task--important" ).addClass( "task--completed" );
                    $("li#delete_button_"+id+".expand-list__item").remove();
                }else{
                    $("tr#"+id+".tasks__item").remove();
                }
                // пересчитываем задачи
                recalcTasksInPorjects();
            }
            // Добавляем сообщение
            addFlash(data.message,data.error);
        },
        error: function (data, status, object) {
            addFlash('При ajax запросе произошла ошибка. Код: ' + data.status + ' Содержание: '+ object);
        }
    });
}

function duplicateOneTask($url){
    var html = '';
    $.ajax({
        type: "POST",
        url:  $url,
        async: "true",
        dataType: "json",
        success: function (data, status, object) {
            if (!data.error) {

                html += drawOneRaw(data);

                // вставляем Html
                $('table.tasks tbody').append(html);
                // пересчитываем задачи
                recalcTasksInPorjects();
            }
            // Добавляем сообщение
            addFlash(data.message,data.error);
        },
        error: function (data, status, object) {
            addFlash('При ajax запросе произошла ошибка. Код: ' + data.status + ' Содержание: '+ object);
        }
    });
}

function recalcTasksInPorjects() {
    $.ajax({
        type: "POST",
        url:  routeRecalc,// глобальная переменная объявленная в шаблоне layout
        async: "true",
        dataType: "json",
        success: function (data, status, object) {
            var arr = data.info;
            arr.forEach(function(item, i, arr) {
                $('a.main-navigation__list-item-link:contains(' + item.project_name + ')').siblings('span').text(item.count_tasks);
            });
            //console.log(data);
        },
        error: function (data, status, object) {
            alert('При ajax запросе произошла ошибка. Код: '+data.status+' Содержание: '+ object);
        }
    });
}

function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

function clearMessageBlock(){
    if ($('*').is('div.messages')) {
        $('div.messages').remove();
    }
}

function drawControls($id, $data){
    var html = '';

    html += '<td class="task__controls">';
    html += '<button class="expand-control" type="button" name="button">Открыть список комманд</button>';
    html += '<ul class="expand-list hidden">';
    html += '<li id="delete_button_34" class="expand-list__item">';
    html += '<a href="#" class="btn btn-md btn-block btn-primary" onclick="completeOneTask('+ $id + ',\'' + $data.done_url +'\');return false;">ВЫПОЛНИТЬ</a>';
    html += '</li>';
    html += '<li class="expand-list__item">';
    html += '<a href="#" class="btn btn-md btn-block btn-danger" onclick="deleteOneTask('+ $id + ',\'' + $data.delete_url +'\');return false;">УДАЛИТЬ</a>';
    html += '</li>';
    html += '<li class="expand-list__item">';
    html += '<a href="#" class="btn btn-md btn-block btn-success" onclick="duplicateOneTask(\''+ $data.clone_url +'\');return false;">ДУБЛИРОВАТЬ</a>';
    html += '</li>';
    html += '<li class="expand-list__item">';
    html += '<a href="' + $data.edit_url + '" class="btn btn-md btn-block btn-info">РЕДАКТИРОВАТЬ</a>';
    html += '</li>';
    html += '</ul>';
    html += '</td>';

    return html;
}

function drawOneRaw(data){
    var out = '';
    // tr
    out += '<tr id="' + data.info.id + '" class="tasks__item task--important">';
    // TaskName
    out += '<td class="task__select">';
    out += '<label class="checkbox task__checkbox">';
    out += '<input class="checkbox__input visually-hidden" type="checkbox">';
    out += '<span class="checkbox__text">'+ data.info.task_name +'</span>';
    out += '</label> </td>';
    // ID
    out += '<td class="task__id">';
    out += '<span>' + data.info.id + '</span>';
    out += '</td>';
    // Project
    out += '<td class="task__project">';
    out += '<a class = "not_href" href="' + data.info.urls.project_url + '">';
    out += '<span>'+ data.info.project_name +'</span>';
    out +=  '</a></td>';
    // File
    out += '<td class="task__file">';
    out += '<span class="download-link">No File</span>';
    out += '</td>';
    // DueDate
    out += '<td class="task__date">'+ data.info.due_date +' </td>';
    // Controls
    out += drawControls(data.info.id, data.info.urls);
    // end tr
    out += '</tr>';

    return out;
}

function addFlash(message,msgType){

    if (msgType === undefined) {
        msgType = 2;
    }

    // преобразуем к числу
    msgType = Number(msgType);

    var alertType = '';

    switch (msgType) {
        case 0:
            alertType = 'success';
            break;
        case 1:
            alertType = 'warning';
            break;
        default:
            alertType = 'danger';
    }
    // очищаем блок если есть
    clearMessageBlock();


    var html = '';
    html += '<div class="messages">';
    html += '<div class="alert alert-dismissible alert-'+ alertType +'" role="alert">';
    html += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
    html += '<span aria-hidden="true">×</span></button>';
    html += message;
    html += '</div></div>';

    $("main.content__main").prepend(html);
}