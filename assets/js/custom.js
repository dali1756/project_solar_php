			function LoadingMask(load_id){
				setTimeout(()=>{
					$(load_id).append(
						`<div class="btn btn-danger" role="button" >
							<span class="spinner-border" role="status" aria-hidden="true"></span>
							<span class="d-block">Loading...</span>
						</div>`
					)
					$(load_id).removeClass("d-none");
				},0)
			}
			function ClearMask(load_id,search_area){
				setTimeout(()=>{
					$(load_id).empty()
					$(load_id).addClass("d-none");
					$(search_area).removeClass("d-none");
				},0)
			}