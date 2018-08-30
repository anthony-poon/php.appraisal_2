import 'datatables.net/js/jquery.dataTables'
import 'datatables.net-bs4';
import 'datatables.net-buttons-bs4/js/buttons.bootstrap4';
import 'datatables.net-select-bs4/js/select.bootstrap4';
import 'datatables.net-responsive-bs4/js/responsive.bootstrap4';
import URI from "urijs";
import 'urijs/src/URITemplate';

export default class EntityTable {
    constructor(options) {
        this.el = options.el;
        this.addPath = $(this.el).data("add-path");
        this.editPath = $(this.el).data("edit-path");
        this.delPath = $(this.el).data("del-path");
    }

    init() {
        this.table = $(this.el).DataTable({
            "select": {
                "style": "single"
            },
            "responsive": true,
            "dom":  "<'row table_btn_grp'<'col-sm-12 col-md-6'B><'col-sm-12 col-md-6'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            "buttons": [
                {
                    text: "Create",
                    className: "create-btn",
                    action: (e, dt, node, config) => {
                        location.href = this.addPath
                    },

                },{
                    text: "Edit",
                    className: "update-btn",
                    action: (e, dt, node, config) => {
                        let id = parseInt(dt.rows(".selected").ids().toArray()[0]);
                        location.href = URI.expand(this.editPath, {
                            id: id
                        });
                    }
                }, {
                    text: "Delete",
                    className: "delete-btn",
                    action: (e, dt, node, config) =>    {
                        let id = parseInt(dt.rows(".selected").ids().toArray()[0]);
                        location.href = URI.expand(this.delPath, {
                            id: id
                        });
                    }
                }
            ],
        });
        this.table.buttons(".delete-btn").disable();
        this.table.buttons(".update-btn").disable();
        this.table.on('select', (e, dt, type, indexes) => {
        if (this.table.rows(".selected")[0].length === 0) {
                this.table.buttons(".read-btn").disable();
                this.table.buttons(".update-btn").disable();
                this.table.buttons(".delete-btn").disable();
            } else if (this.table.rows(".selected")[0].length === 1) {
                this.table.buttons(".read-btn").enable();
                this.table.buttons(".update-btn").enable();
                this.table.buttons(".delete-btn").enable();
            } else {
                this.table.buttons(".read-btn").disable();
                this.table.buttons(".update-btn").disable();
                this.table.buttons(".delete-btn").enable();
            }
        });
        this.table.on('deselect', (e, dt, type, indexes) => {
            if (this.table.rows(".selected")[0].length === 0) {
                this.table.buttons(".read-btn").disable();
                this.table.buttons(".update-btn").disable();
                this.table.buttons(".delete-btn").disable();
            } else if (this.table.rows(".selected")[0].length === 1) {
                this.table.buttons(".read-btn").enable();
                this.table.buttons(".update-btn").enable();
                this.table.buttons(".delete-btn").enable();
            } else {
                this.table.buttons(".read-btn").disable();
                this.table.buttons(".update-btn").disable();
                this.table.buttons(".delete-btn").enable();
            }
        });
    }
}