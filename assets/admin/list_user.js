import CRUDTable from '../share/crud_table';
import axios from 'axios';
import * as URI from 'urijs';
import "urijs/src/URITemplate";

$(document).ready(function(){
    let table = new CRUDTable({
        element: "#user-table",
        // https://datatables.net/extensions/buttons/custom
        createAction: function(e, dt, node, config) {
            location.href = Param.addPath;
        },
        updateAction: function(e, dt, node, config){
            let id = dt.rows(".selected").ids().toArray()[0];
            location.href = URI.expand(Param.editPath, {
                id: id
            });
        },
        deleteAction: function(e, dt, node, config) {
            axios.delete(Param.deletePath, {
                data: dt.rows(".selected").ids().toArray()
            }).then((ajax) => {
                location.reload();
            }).catch((ajax) => {
                console.log(ajax);
                alert("Deletion failed")
            })
        },
        btn: [{
            text: "Upload",
            action: () => {
                location.href = Param.uploadPath
            }
        }]
    });

    table.init();
});

