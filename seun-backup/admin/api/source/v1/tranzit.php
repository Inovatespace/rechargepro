 try
                    {
                        WebClient wc = new WebClient();
                        wc.Encoding = Encoding.UTF8;
                        string xml = response.Content;//wc.DownloadString("http://localhost/plenty/api/v1/request/load_log.xml");
                        XmlSerializer xs = new XmlSerializer(typeof(List<string>));
                        StringReader sr = new StringReader(xml);

                        XmlTextReader xtr = new XmlTextReader(sr);

                        DataSet ds = new DataSet();
                        ds.ReadXml(xtr);
                        ds.Tables[0].Columns.Remove("client");
                        ds.Tables[0].Columns.Remove("time");
                        ds.Tables[0].Columns.Add("STATUS", typeof(Bitmap));
                        //ds.Tables[0].Columns.RemoveAt(6);
                        //ds.Tables[0].Columns.RemoveAt(7);
                        if (ds != null)
                        {


                            for (int i = ds.Tables[0].Rows.Count; i < 19; i++)
                            {
                                // DataRow newrow = ds.Tables[0].NewRow();
                                // ds.Tables[0].Rows.Add(newrow);
                            }


                            //BindingSource bs = new BindingSource();
                            // bs.DataSource = ds;
                            // bs.DataMember = ds.Tables[0].TableName;



                            ds.AcceptChanges();
                            dataGridView1.DataSource = ds;
                            dataGridView1.DataMember = ds.Tables[0].TableName;


                            //dataGridView1.Columns["client"].Visible = false;
                            //dataGridView1.Columns["time"].Visible = false;
                            dataGridView1.Columns["sync"].Visible = false;
                            dataGridView1.Columns["id"].Visible = false;

                            //ADD CELL
                            DataGridViewImageColumn ImageColumn = new DataGridViewImageColumn();
                            ImageColumn.DefaultCellStyle.NullValue = null;//imageList1.Images[0];
                            ImageColumn.Name = "STATUS";
                            ImageColumn.HeaderText = "";
                            //dataGridView1.Columns.Insert(5, ImageColumn);

                            //dataGridView1.Columns[0].HeaderCell.Value = "REF";
                            // dataGridView1.Columns[1].HeaderCell.Value = "AMOUNT";
                            //dataGridView1.Columns[2].HeaderCell.Value = "MOBILE";
                            //dataGridView1.Columns[3].HeaderCell.Value = "DETAILS";
                            // dataGridView1.Columns[4].HeaderCell.Value = "DATE";

                            foreach (DataGridViewRow row1 in dataGridView1.Rows)
                            {
                               //  MessageBox.Show(row1.Cells[5].Value.ToString());
                                if (row1.Cells[5].Value.Equals("1"))
                                {
                                    row1.Cells[7].Value = imageList1.Images[1];

                                }else{
                                    row1.Cells[7].Value = imageList1.Images[2];
                                }
                            }



                            dataGridView1.Update();
                            dataGridView1.Refresh();
                            dataGridView1.RefreshEdit();


                            /////////////////////////////////////////////////


                            dataGridView1.CurrentCell = null;
                            dataGridView1.Rows[0].Visible = false;
                            dataGridView1.Rows[1].Visible = false;
                            foreach (DataGridViewColumn column1 in dataGridView1.Columns)
                            {
                                column1.SortMode = DataGridViewColumnSortMode.NotSortable;
                            }


                        }

                    }
                    catch (Exception ex)
                    {

                        

                        DataSet ds = new DataSet();
                        ds.Tables.Add("downloads");
                        ds.Tables[0].Columns.Add("REF", typeof(string));
                        ds.Tables[0].Columns.Add("AMOUNT", typeof(string));
                        ds.Tables[0].Columns.Add("MOBILE", typeof(string));
                        ds.Tables[0].Columns.Add("DETAILS", typeof(string));
                        ds.Tables[0].Columns.Add("DATE", typeof(string));


                        for (int i = 0; i < 19; i++)
                        {
                            DataRow newrow = ds.Tables[0].NewRow();
                            ds.Tables[0].Rows.Add(newrow);
                        }


                        ds.AcceptChanges();
                        dataGridView1.DataSource = ds;
                        dataGridView1.DataMember = "downloads";

                    }