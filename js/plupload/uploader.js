var uploader = {
	plupload: undefined,

	params: undefined,

	count: 0,

	max_count: 0,

	postId: '',

	init: function(params)
	{
		uploader.params = params;

		uploader.plupload = new plupload.Uploader({
			runtimes : 'html5,html4',
			browse_button: uploader.params.button,
			container: uploader.params.container,
			max_file_size : uploader.params.max_file_size + 'mb',
			url : uploader.params.url,
			flash_swf_url : '/scripts/plupload/Moxie.swf',
			filters : [
				{title : "Image files", extensions : "jpg,jpeg,gif,png"}
			],
			multipart_params: {action: 'upload', uuid: params.uuid}
		});

		uploader.plupload.init();

		if (uploader.count >= uploader.max_count)
		{
			$('#' + uploader.params.button ).hide();
		}

		uploader.plupload.bind('FilesAdded', function(up, files) {
			up.refresh();
			up.start();
		});

		uploader.plupload.bind('BeforeUpload', function(up, file) {

			$('#' + uploader.params.button ).addClass('button_in_action');

			if (uploader.count >= uploader.max_count)
			{
				$('#' + uploader.params.button ).hide();
				up.stop();
				$('#' + uploader.params.button ).removeClass('button_in_action');
			}
		});
		uploader.plupload.bind('FileUploaded', function(up, file, info)
		{
			/** Для злоебучего FF */
			var response = info.response.replace(/^.*?\{/, '{').replace(/\}.*$/, '}');
			if (response.indexOf('Превышен лимит') > 0)
			{
				alert("Не удалось загрузить файл на сервер. Размер файла слишком большой.");
			}
			else
			{
				var obj = JSON.parse(response);
				if (obj.success)
				{
					uploader.count++;
					if (uploader.count >= uploader.max_count)
					{
						$('#' + uploader.params.button ).hide();
						up.stop();
					}
					$('#' + uploader.params.container).append(
						'<div class="uploader_file" id="' + obj.key + '"><a href="'+obj.url+'" target="_blank"><img src="'+obj.url_preview+'" /></a><div class="uploader_close" title="Удалить" onclick="uploader.fileDelete(\''+obj.key+'\', \''+ obj.name +'\')"></div>'+'<input type="hidden" name="sort[]" value="'+obj.name+'">'+'</div>'
						);

					$( "#i_uploader_container_multi" ).sortable();
					$( "#i_uploader_container_multi" ).disableSelection();
				}
				else
				{
					alert(obj.error);
				}
			}

			$('#' + uploader.params.button ).removeClass('button_in_action');
			up.refresh();
		});
	},

	fileDelete: function(id, key)
	{
		$.ajax( {
			url     : uploader.params.url,
			type    : 'POST',
			mode    : 'abort',
			dataType: 'json',
			data    : {
				action    : 'delete',
				postId: uploader.postId,
				uuid : uploader.params.uuid,
				key     : key
			},
			success : function ( data )
			{
				if (data.success)
				{
					uploader.count--;
					$('#'+id ).remove();
					var file = uploader.plupload.getFile(id);
					if (file)
					{
						uploader.plupload.removeFile(file);
					}
					if (uploader.count < uploader.max_count)
					{
						$('#' + uploader.params.button ).show();
					}
					uploader.plupload.refresh();
				}
			},
			failed  : function ()
			{
				alert('Не удалось удалить файл. Попробуйте позднее');
			}
		} );
	}
};