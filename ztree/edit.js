var setting = {
    view: {
        dblClickExpand: false,
        showLine: true,
        selectedMulti: true,
        showIcon: false
    },
    data: {
        keep: {
            leaf: true,
            parent: true
        },
        simpleData: {
            enable:true,
            idKey: "id",
            pIdKey: "pId",
            rootPId: ""
        }
    },
    edit: {
        enable: true,
        editNameSelectAll: true,
        removeTitle: '删除节点',
        renameTitle: '编辑节点标题'
    },
    callback: {
        beforeClick: function(treeId, treeNode) {
            var zTree = $.fn.zTree.getZTreeObj("tree");
            if (treeNode.isParent) {
                zTree.expandNode(treeNode);
                return false;
            }
        }
    }
};

var zNodes =[
    {id:1, pId:0, name:"[core] 基本功能 演示", open:true},
    {id:101, pId:1, name:"最简单的树 --  标准 JSON 数据", file:"core/standardData"},
    {id:102, pId:1, name:"最简单的树 --  简单 JSON 数据", file:"core/simpleData"},

    {id:2, pId:0, name:"[excheck] 复/单选框功能 演示", open:false},
    {id:201, pId:2, name:"Checkbox 勾选操作", file:"excheck/checkbox"},
    {id:206, pId:2, name:"Checkbox nocheck 演示", file:"excheck/checkbox_nocheck"},
    {id:207, pId:2, name:"Checkbox chkDisabled 演示", file:"excheck/checkbox_chkDisabled"}
];

$(document).ready(function(){
    var t = $("#tree");
    var zTree = $.fn.zTree.init(t, setting, zNodes);
    zTree.selectNode(zTree.getNodeByParam("id", 101));

    $("#grid").bind("mousedown",$.myGrid.gridMouseDown);
});

$.myGrid = {};
$.myGrid.minMoveSize = 5;

$.myGrid.tmpTreeSetting = {
    view: {
        selectedMulti: true,
        showIcon: false
    },
    data: {
        simpleData: {
            enable:true,
            idKey: "id",
            pIdKey: "pId",
            rootPId: ""
        }
    },
    edit: {
        enable: true
    },
    callback: {
        onDrop: function(event, treeId, treeNodes, targetNode, moveType) {
            var tmpTree = $.fn.zTree.getZTreeObj("tmpTree");
            tmpTree.destroy();
            $.myGrid.tmpTree = null;

            $("#grid").unbind("mousemove");
            console.log("drop node");
        }
    }
};

$.myGrid.gridMouseDown = function (event) {
    this.mouseDownX = event.clientX;
    this.mouseDownY = event.clientY;
    $("#grid").bind("mousemove",$.myGrid.gridMouseMove);
};

$.myGrid.gridMouseMove = function (event) {
    if (Math.abs(this.mouseDownX - event.clientX) < this.minMoveSize
        && Math.abs(this.mouseDownY - event.clientY) < this.minMoveSize) {
        return false;
    }

    if ( ! $.myGrid.tmpTree) {
        var tmpTree = $("#tmpTree");

        var zNodes =[
            {id:1, pId:0, name:"测试节点1"},
            {id:101, pId:1, name:"测试节点11"},
            {id:102, pId:1, name:"测试节点12"},

            {id:2, pId:0, name:"测试节点2"},
            {id:201, pId:2, name:"测试节点21"},
            {id:202, pId:2, name:"测试节点22"}
        ];

        var tree = $.fn.zTree.init(tmpTree, $.myGrid.tmpTreeSetting , zNodes);

        //选中全部节点
        var allNodes = tree.getNodes();
        for (var i = 0; i < allNodes.length; i++) {
            tree.selectNode(allNodes[i], true);
        }

        //触发「mousedown」事件，「target」设置为第0节点(为了达到「ztree」事件触发条件)
        var mouseDownevent = jQuery.Event( "mousedown", {"treeId": "tmpTree"});
        mouseDownevent.target = $("#" + allNodes[0].tId + $.fn.zTree.consts.id.A).get(0);
        tree.setting.treeObj.trigger(mouseDownevent);

        $.myGrid.tmpTree = tree;
    }
}

$(document).ready(function(){
    //grid 注册事件
    $("#grid").bind("mousedown",$.myGrid.gridMouseDown);
});
