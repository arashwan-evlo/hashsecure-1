<style>
    .chart-content{
        border:3px solid rgb(255, 255, 255);
        background-color: #FFF;
        border-radius: 10px;
        padding: 18px;
        max-width: 98%;
        margin: auto;
        border-top-color: #958b7c;
    }
.tree-table>.tree{
    font-size: 17px;
}
.tree-table>.tree ul{
        list-style: none;
        line-height:2.6em;
    }
    .node-treeview {
        cursor:pointer ;
    }

    .node-treeview summary::marker{
        display: none;
    }
    .node-treeview summary::-webkit-details-marker{
        display: none;
    }

    .tree ul li{
        position:relative;

    }

    .tree ul li::before{
        position: absolute;
        right: -35px;
        top:0px;
        border-right:2px solid #4b534b;
        border-bottom: 2px solid #4b534b;
        content:"";
        width: 21px;
        height: 1.3em;
    }
  .tree ul li::after{
      position: absolute;
      right: -35px;
      bottom:0px;
      border-right:2px solid #4b534b;
      content:"";
      width: 21px;
      height: 100%;
  }
  .tree ul li:last-child::after{
      display: none;
  }

  ul.node-treeview>li:after,ul.node-treeview>li:before{
      display: none;
  }

  .main-chart ul{
      position:absolute;
  }
  ul summary::before{
      position: absolute;
      right: -30px;
      top: 5px;
      content: "+";
      background: #4b534b;
      width: 20px;
      height: 20px;
      border-radius: 50%;
      margin-top: 4px;
      margin-right: 3px;
      text-align: center;
      line-height: 0.8;
      color: #fdfdfd;
      font-size: 20px;
  }
ul details[open]>summary::before{
    content: "-";
}


summary ul{
    margin-right: 50px;
}
  .chart-chiled-x::before{
          position: absolute;
          right: -30px;
          top: 5px;
          content: "+";
          background: #4b534b;
          width: 15px;
          height: 15px;
          border-radius: 50%;
          margin-top: 7px;
          margin-right: 7px;

  }

    .node-treeviewx:hover{
        background-color: #AE0E0E!important;
        color: white!important;
    }


.selected{
    color: red;
}
.accoun-x{
    transition: font-size 2s ease-in-out;
}
.account:hover{
    color: red;
    font-size:1.08em ;
}
.account_code{
    width: 10px;
}

.account-table{
    font-size: 18px;
    font-weight: 600;
    line-height: 1.2;
}


.account_name{
    cursor:pointer ;
}
    .account_name:hover{
        font-size: 18.5px;
        color: #585252;
    }

.account-logo{
    font-size: 20px;
}

.account_loader{
    display: block;
    font-weight: 700;
    text-align: center;
    font-size: 30px;
    color: #59665b;
}

.account{
    font-weight: 600 !important;
}

.account-link{
    text-align: left;
    font-weight: 600 !important;
}

</style>

</style>